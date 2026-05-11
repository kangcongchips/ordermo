CREATE DATABASE IF NOT EXISTS ordermo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ordermo;

DROP TABLE IF EXISTS merchants;
DROP TABLE IF EXISTS riders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;
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

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'rider', 'merchant') NOT NULL,
    status ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB;

-- Default admin: username=admin, password=admin123
INSERT INTO admins (username, password_hash, full_name) VALUES
    ('admin', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'Administrator');

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    default_address VARCHAR(255) DEFAULT NULL,
    city_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE riders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    vehicle_type ENUM('motorcycle', 'bicycle', 'car') NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    application_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    is_available TINYINT(1) NOT NULL DEFAULT 0,
    current_lat DECIMAL(10, 7) DEFAULT NULL,
    current_lng DECIMAL(10, 7) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_application_status (application_status),
    INDEX idx_is_available (is_available)
) ENGINE=InnoDB;

CREATE TABLE merchants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    business_name VARCHAR(150) NOT NULL,
    business_address VARCHAR(255) NOT NULL,
    city_id INT DEFAULT NULL,
    application_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    is_open TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL,
    INDEX idx_application_status (application_status),
    INDEX idx_is_open (is_open)
) ENGINE=InnoDB;
