<?php

class City
{
    public function getGroupedByProvince(): array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->query(
                'SELECT id, name, image, province FROM cities
                 WHERE featured = 1
                 ORDER BY FIELD(province, "Cebu"), name'
            );
            $rows = $stmt->fetchAll();

            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row['province']][] = $row;
            }
            return $grouped;
        } catch (Throwable $e) {
            return $this->fallback();
        }
    }

    /** Flat list of every city (id, name, province) for admin selects. */
    public function all(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT id, name, province FROM cities ORDER BY province, name'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /** A single city by id, or null if missing / DB unavailable. */
    public function find(int $id): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT id, name, image, province FROM cities WHERE id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            foreach ($this->fallback() as $province => $cities) {
                foreach ($cities as $c) {
                    if ((int) $c['id'] === $id) {
                        return $c + ['province' => $province];
                    }
                }
            }
            return null;
        }
    }

    private function fallback(): array
    {
        return [
            'Cebu' => [
                ['id' => 1, 'name' => 'Cebu City',      'image' => 'cebu-city.jpg'],
                ['id' => 2, 'name' => 'Mandaue City',   'image' => 'mandaue.jpg'],
                ['id' => 3, 'name' => 'Lapu-Lapu City', 'image' => 'lapu-lapu.jpg'],
                ['id' => 4, 'name' => 'Talisay City',   'image' => 'talisay.jpg'],
                ['id' => 5, 'name' => 'Toledo City',    'image' => 'toledo.jpg'],
                ['id' => 6, 'name' => 'Carcar City',    'image' => 'carcar.jpg'],
                ['id' => 7, 'name' => 'Danao City',     'image' => 'danao.jpg'],
                ['id' => 8, 'name' => 'Naga City',      'image' => 'naga.jpg'],
            ],
        ];
    }
}
