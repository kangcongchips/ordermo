<?php

class Rider
{
    /** All riders joined with their user account, newest first. */
    public function allWithUser(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT r.id, r.vehicle_type, r.license_number,
                        r.application_status, r.is_available,
                        u.first_name, u.last_name, u.email, u.phone, u.status,
                        r.created_at
                 FROM riders r
                 JOIN users u ON u.id = r.user_id
                 ORDER BY r.id DESC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Rider login lookup: the rider profile + the user account behind it,
     * matched by email. Any application status. Null if no such rider.
     */
    public function findForLogin(string $email): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT r.id AS rider_id, r.vehicle_type, r.license_number,
                        r.application_status, r.is_available,
                        u.id AS user_id, u.first_name, u.last_name,
                        u.password_hash, u.status
                 FROM users u
                 JOIN riders r ON r.user_id = u.id
                 WHERE u.email = :e AND u.role = "rider"
                 LIMIT 1'
            );
            $stmt->execute([':e' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** The signed-in rider's full profile (any status), or null. */
    public function profileByUserId(int $userId): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT r.*, u.first_name, u.last_name, u.email, u.phone
                 FROM riders r
                 JOIN users u ON u.id = r.user_id
                 WHERE r.user_id = :u
                 LIMIT 1'
            );
            $stmt->execute([':u' => $userId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** Toggle whether the rider is accepting deliveries. */
    public function setAvailability(int $riderId, bool $available): bool
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare('UPDATE riders SET is_available = ? WHERE id = ?');
            return $stmt->execute([$available ? 1 : 0, $riderId]);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Create the user account + rider profile in one transaction.
     *
     * @param array $d first_name,last_name,email,phone,password,
     *                  vehicle_type,license_number,application_status
     */
    public function create(array $d): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status)
                 VALUES (?, ?, ?, ?, ?, "rider", "active")'
            );
            $stmt->execute([
                $d['first_name'], $d['last_name'], $d['email'], $d['phone'],
                password_hash($d['password'], PASSWORD_BCRYPT),
            ]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare(
                'INSERT INTO riders (user_id, vehicle_type, license_number, application_status)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId, $d['vehicle_type'], $d['license_number'], $d['application_status'],
            ]);

            $db->commit();
            return (int) $db->lastInsertId();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
