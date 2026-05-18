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
     *                  application_status
     */
    public function create(array $d): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status)
                 VALUES (?, ?, ?, ?, ?, "merchant", "active")'
            );
            $stmt->execute([
                $d['first_name'], $d['last_name'], $d['email'], $d['phone'],
                password_hash($d['password'], PASSWORD_BCRYPT),
            ]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare(
                'INSERT INTO merchants
                    (user_id, business_name, business_address, city_id, cuisine,
                     application_status, is_open)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                $d['business_name'],
                $d['business_address'],
                $d['city_id'] !== '' ? (int) $d['city_id'] : null,
                $d['cuisine'],
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
