<?php

class Merchant
{
    /** Approved, listable restaurants for a city. */
    public function getByCity(int $cityId): array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT id, business_name, business_address, cuisine, cover_image,
                        rating, rating_count, prep_min, prep_max, free_delivery,
                        price_level, opens_at, closes_at, is_open
                 FROM merchants
                 WHERE city_id = :city AND application_status = "approved"
                 ORDER BY is_open DESC, rating DESC, rating_count DESC'
            );
            $stmt->execute([':city' => $cityId]);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /** A single restaurant joined with its city name, or null. */
    public function find(int $id): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT m.*, c.name AS city_name, c.id AS city_id
                 FROM merchants m
                 LEFT JOIN cities c ON c.id = m.city_id
                 WHERE m.id = :id AND m.application_status = "approved"
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** All merchants (any status) joined with owner + city, for admin. */
    public function allWithUser(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT m.id, m.business_name, m.business_address, m.cuisine,
                        m.application_status, m.is_open, m.rating,
                        c.name AS city_name,
                        u.first_name, u.last_name, u.email, u.phone,
                        m.created_at
                 FROM merchants m
                 JOIN users u ON u.id = m.user_id
                 LEFT JOIN cities c ON c.id = m.city_id
                 ORDER BY m.id DESC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Owner login lookup: the merchant profile + the user account behind it,
     * matched by email. Any application status (a pending owner can still see
     * their own dashboard). Null if no such merchant.
     */
    public function findForLogin(string $email): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT m.id AS merchant_id, m.business_name,
                        m.application_status, m.is_open,
                        u.id AS user_id, u.first_name, u.last_name,
                        u.password_hash, u.status
                 FROM users u
                 JOIN merchants m ON m.user_id = u.id
                 WHERE u.email = :e AND u.role = "merchant"
                 LIMIT 1'
            );
            $stmt->execute([':e' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** The signed-in owner's full restaurant profile (any status), or null. */
    public function profileByUserId(int $userId): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT m.*, c.name AS city_name,
                        u.first_name, u.last_name, u.email, u.phone
                 FROM merchants m
                 JOIN users u ON u.id = m.user_id
                 LEFT JOIN cities c ON c.id = m.city_id
                 WHERE m.user_id = :u
                 LIMIT 1'
            );
            $stmt->execute([':u' => $userId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Headline numbers for the owner dashboard.
     *
     * @return array{orders:int,pending:int,menu_items:int,revenue:float}
     */
    public function statsForMerchant(int $merchantId): array
    {
        try {
            $db = Database::getConnection();

            $count = function (string $sql) use ($db, $merchantId): int {
                $stmt = $db->prepare($sql);
                $stmt->execute([':m' => $merchantId]);
                return (int) $stmt->fetchColumn();
            };

            $rev = $db->prepare(
                'SELECT COALESCE(SUM(total), 0) FROM orders
                 WHERE merchant_id = :m AND status = "delivered"'
            );
            $rev->execute([':m' => $merchantId]);

            return [
                'orders'     => $count('SELECT COUNT(*) FROM orders WHERE merchant_id = :m'),
                'pending'    => $count('SELECT COUNT(*) FROM orders WHERE merchant_id = :m AND status = "pending"'),
                'menu_items' => $count('SELECT COUNT(*) FROM menu_items WHERE merchant_id = :m'),
                'revenue'    => (float) $rev->fetchColumn(),
            ];
        } catch (Throwable $e) {
            return ['orders' => 0, 'pending' => 0, 'menu_items' => 0, 'revenue' => 0.0];
        }
    }

    /**
     * Set the application status (pending/approved/rejected). Keeps the owner
     * user account's login status in sync so a rejected/pending owner can't
     * sign in until they're approved.
     */
    public function updateApplicationStatus(int $merchantId, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return false;
        }
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                'UPDATE merchants SET application_status = ?, is_open = ? WHERE id = ?'
            );
            $stmt->execute([$status, $status === 'approved' ? 1 : 0, $merchantId]);

            $stmt = $db->prepare(
                'UPDATE users u
                 JOIN merchants m ON m.user_id = u.id
                 SET u.status = ?
                 WHERE m.id = ?'
            );
            $stmt->execute([self::userStatusFor($status), $merchantId]);

            $db->commit();
            return true;
        } catch (Throwable $e) {
            $db->rollBack();
            return false;
        }
    }

    /** Map application_status → users.status for the owner account. */
    private static function userStatusFor(string $applicationStatus): string
    {
        return match ($applicationStatus) {
            'approved' => 'active',
            'rejected' => 'suspended',
            default    => 'pending',
        };
    }

    /** Pending merchant registrations for the admin dashboard. */
    public function pendingApplications(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT m.id, m.business_name, m.business_address, m.cuisine,
                        m.application_status, c.name AS city_name,
                        u.first_name, u.last_name, u.email, u.phone, m.created_at
                 FROM merchants m
                 JOIN users u ON u.id = m.user_id
                 LEFT JOIN cities c ON c.id = m.city_id
                 WHERE m.application_status = "pending"
                 ORDER BY m.created_at ASC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /** Count of pending merchant applications. */
    public function pendingCount(): int
    {
        try {
            $db = Database::getConnection();
            return (int) $db->query(
                'SELECT COUNT(*) FROM merchants WHERE application_status = "pending"'
            )->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }

    /** Open or close the restaurant for new orders. */
    public function setOpen(int $merchantId, bool $open): bool
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare('UPDATE merchants SET is_open = ? WHERE id = ?');
            return $stmt->execute([$open ? 1 : 0, $merchantId]);
        } catch (Throwable $e) {
            return false;
        }
    }

    /** Minimal id/name list (any status) for select dropdowns. */
    public function listForSelect(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT id, business_name FROM merchants ORDER BY business_name'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Create the owner user account + merchant profile in one transaction.
     *
     * @param array $d first_name,last_name,email,phone,password,
     *                  business_name,business_address,city_id,cuisine,
     *                  cover_image,application_status
     */
    public function create(array $d): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $userStatus = self::userStatusFor($d['application_status']);
            $stmt = $db->prepare(
                'INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status)
                 VALUES (?, ?, ?, ?, ?, "merchant", ?)'
            );
            $stmt->execute([
                $d['first_name'], $d['last_name'], $d['email'], $d['phone'],
                password_hash($d['password'], PASSWORD_BCRYPT),
                $userStatus,
            ]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare(
                'INSERT INTO merchants
                    (user_id, business_name, business_address, city_id, cuisine,
                     cover_image, application_status, is_open)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                $d['business_name'],
                $d['business_address'],
                $d['city_id'] !== '' ? (int) $d['city_id'] : null,
                $d['cuisine'],
                ($d['cover_image'] ?? '') !== '' ? $d['cover_image'] : null,
                $d['application_status'],
                $d['application_status'] === 'approved' ? 1 : 0,
            ]);
            $merchantId = (int) $db->lastInsertId();

            $db->commit();
            return $merchantId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
