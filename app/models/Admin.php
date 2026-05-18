<?php

class Admin
{
    public function findByUsername(string $username): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function touchLastLogin(int $id): void
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Counts used on the admin dashboard.
     *
     * @return array{customers:int,merchants:int,riders:int,orders:int}
     */
    public function dashboardStats(): array
    {
        $db = Database::getConnection();

        return [
            'customers' => (int) $db->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
            'merchants' => (int) $db->query('SELECT COUNT(*) FROM merchants')->fetchColumn(),
            'riders'    => (int) $db->query('SELECT COUNT(*) FROM riders')->fetchColumn(),
            'orders'    => (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
        ];
    }
}
