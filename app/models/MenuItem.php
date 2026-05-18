<?php

class MenuItem
{
    /** Available menu items for a restaurant, grouped by category. */
    public function getByMerchantGrouped(int $merchantId): array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT id, name, description, price, image, category
                 FROM menu_items
                 WHERE merchant_id = :m AND is_available = 1
                 ORDER BY category, name'
            );
            $stmt->execute([':m' => $merchantId]);
            $rows = $stmt->fetchAll();

            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row['category']][] = $row;
            }
            return $grouped;
        } catch (Throwable $e) {
            return [];
        }
    }

    /** A single available menu item joined with its restaurant name, or null. */
    public function find(int $id): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT mi.id, mi.merchant_id, mi.name, mi.description,
                        mi.price, mi.image, mi.category,
                        m.business_name, m.free_delivery
                 FROM menu_items mi
                 JOIN merchants m ON m.id = mi.merchant_id
                 WHERE mi.id = :id AND mi.is_available = 1
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** All menu items (any availability) joined with merchant, for admin. */
    public function allWithMerchant(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT mi.id, mi.name, mi.price, mi.category,
                        mi.is_available, m.business_name, mi.created_at
                 FROM menu_items mi
                 JOIN merchants m ON m.id = mi.merchant_id
                 ORDER BY mi.id DESC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Create a menu item.
     *
     * @param array $d merchant_id,name,description,price,category,
     *                  image,is_available
     */
    public function create(array $d): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO menu_items
                (merchant_id, name, description, price, image, category, is_available)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $d['merchant_id'],
            $d['name'],
            $d['description'],
            (float) $d['price'],
            $d['image'] !== '' ? $d['image'] : null,
            $d['category'],
            !empty($d['is_available']) ? 1 : 0,
        ]);
        return (int) $db->lastInsertId();
    }
}
