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
}
