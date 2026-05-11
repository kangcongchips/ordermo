CREATE DATABASE IF NOT EXISTS ordermo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ordermo;

CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO cities (name, image, featured) VALUES
    ('Manila',  'manila.jpg',  1),
    ('Cebu',    'cebu.jpg',    1),
    ('Davao',   'davao.jpg',   1),
    ('Baguio',  'baguio.jpg',  1);
