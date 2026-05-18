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
}
