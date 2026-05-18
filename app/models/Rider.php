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
