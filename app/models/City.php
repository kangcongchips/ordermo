<?php

class City
{
    public function getFeatured(): array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->query('SELECT id, name, image FROM cities WHERE featured = 1 ORDER BY name');
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [
                ['id' => 1, 'name' => 'Manila',    'image' => 'manila.jpg'],
                ['id' => 2, 'name' => 'Cebu',      'image' => 'cebu.jpg'],
                ['id' => 3, 'name' => 'Davao',     'image' => 'davao.jpg'],
                ['id' => 4, 'name' => 'Baguio',    'image' => 'baguio.jpg'],
            ];
        }
    }
}
