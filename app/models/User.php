<?php

class User
{
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '9')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }

    public function findById(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPhoneAndRole(string $phone, string $role): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE phone = ? AND role = ? LIMIT 1');
        $stmt->execute([$phone, $role]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createCustomer(array $data): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status)
                 VALUES (?, ?, ?, ?, ?, "customer", "active")'
            );
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                password_hash($data['password'], PASSWORD_BCRYPT),
            ]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare('INSERT INTO customers (user_id) VALUES (?)');
            $stmt->execute([$userId]);

            $db->commit();
            return $userId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
