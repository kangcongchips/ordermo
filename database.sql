CREATE DATABASE IF NOT EXISTS ordermo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ordermo;

DROP TABLE IF EXISTS cities;

CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_province (province),
    INDEX idx_featured (featured)
) ENGINE=InnoDB;

INSERT INTO cities (name, province, image, featured) VALUES
    -- Zambales
    ('Castillejos',        'Zambales', 'castillejos.jpg',  1),
    ('Iba',                'Zambales', 'iba.jpg',          1),
    ('Olongapo City',      'Zambales', 'olongapo.jpg',     1),
    ('San Marcelino',      'Zambales', 'san-marcelino.jpg',1),
    ('Subic',              'Zambales', 'subic.jpg',        1),
    ('Subic Bay Freeport', 'Zambales', 'subic-bay.jpg',    1),

    -- Bataan
    ('Abucay',       'Bataan', 'abucay.jpg',       1),
    ('Bagac',        'Bataan', 'bagac.jpg',        1),
    ('Balanga City', 'Bataan', 'balanga.jpg',      1),
    ('Dinalupihan',  'Bataan', 'dinalupihan.jpg',  1),
    ('Hermosa',      'Bataan', 'hermosa.jpg',      1),
    ('Limay',        'Bataan', 'limay.jpg',        1),

    -- Bulacan
    ('Balagtas',        'Bulacan', 'balagtas.jpg',   1),
    ('Baliwag City',    'Bulacan', 'baliwag.jpg',    1),
    ('Angat',           'Bulacan', 'angat.jpg',      1),
    ('Bustos',          'Bulacan', 'bustos.jpg',     1),
    ('Malolos City',    'Bulacan', 'malolos.jpg',    1),
    ('Meycauayan City', 'Bulacan', 'meycauayan.jpg', 1),

    -- Pampanga
    ('Candaba',      'Pampanga', 'candaba.jpg',       1),
    ('Floridablanca','Pampanga', 'floridablanca.jpg', 1),
    ('Guagua',       'Pampanga', 'guagua.jpg',        1),
    ('Lubao',        'Pampanga', 'lubao.jpg',         1);
