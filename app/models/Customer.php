<?php

class Customer
{
    /** The customer profile row for a given users.id, or null. */
    public function findByUserId(int $userId): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT * FROM customers WHERE user_id = ? LIMIT 1'
            );
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** All customers joined with their user account + city, for admin. */
    public function allWithUser(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT cu.id, u.first_name, u.last_name, u.email, u.phone,
                        u.status, cu.default_address, c.name AS city_name,
                        u.created_at
                 FROM customers cu
                 JOIN users u ON u.id = cu.user_id
                 LEFT JOIN cities c ON c.id = cu.city_id
                 ORDER BY cu.id DESC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}
