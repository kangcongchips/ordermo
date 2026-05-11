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
                 ORDER BY FIELD(province, "Zambales", "Bataan", "Bulacan", "Pampanga"), name'
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

    private function fallback(): array
    {
        return [
            'Zambales' => [
                ['id' => 1,  'name' => 'Castillejos',        'image' => 'castillejos.jpg'],
                ['id' => 2,  'name' => 'Iba',                'image' => 'iba.jpg'],
                ['id' => 3,  'name' => 'Olongapo City',      'image' => 'olongapo.jpg'],
                ['id' => 4,  'name' => 'San Marcelino',      'image' => 'san-marcelino.jpg'],
                ['id' => 5,  'name' => 'Subic',              'image' => 'subic.jpg'],
                ['id' => 6,  'name' => 'Subic Bay Freeport', 'image' => 'subic-bay.jpg'],
            ],
            'Bataan' => [
                ['id' => 7,  'name' => 'Abucay',         'image' => 'abucay.jpg'],
                ['id' => 8,  'name' => 'Bagac',          'image' => 'bagac.jpg'],
                ['id' => 9,  'name' => 'Balanga City',   'image' => 'balanga.jpg'],
                ['id' => 10, 'name' => 'Dinalupihan',    'image' => 'dinalupihan.jpg'],
                ['id' => 11, 'name' => 'Hermosa',        'image' => 'hermosa.jpg'],
                ['id' => 12, 'name' => 'Limay',          'image' => 'limay.jpg'],
            ],
            'Bulacan' => [
                ['id' => 13, 'name' => 'Balagtas',         'image' => 'balagtas.jpg'],
                ['id' => 14, 'name' => 'Baliwag City',     'image' => 'baliwag.jpg'],
                ['id' => 15, 'name' => 'Angat',            'image' => 'angat.jpg'],
                ['id' => 16, 'name' => 'Bustos',           'image' => 'bustos.jpg'],
                ['id' => 17, 'name' => 'Malolos City',     'image' => 'malolos.jpg'],
                ['id' => 18, 'name' => 'Meycauayan City',  'image' => 'meycauayan.jpg'],
            ],
            'Pampanga' => [
                ['id' => 19, 'name' => 'Candaba',      'image' => 'candaba.jpg'],
                ['id' => 20, 'name' => 'Floridablanca','image' => 'floridablanca.jpg'],
                ['id' => 21, 'name' => 'Guagua',       'image' => 'guagua.jpg'],
                ['id' => 22, 'name' => 'Lubao',        'image' => 'lubao.jpg'],
            ],
        ];
    }
}
