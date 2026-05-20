CREATE DATABASE IF NOT EXISTS ordermo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ordermo;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS merchants;
DROP TABLE IF EXISTS riders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cities;
SET FOREIGN_KEY_CHECKS = 1;

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
    -- Cebu
    ('Cebu City',      'Cebu', 'cebu-city.jpg',  1),
    ('Mandaue City',   'Cebu', 'mandaue.jpg',    1),
    ('Lapu-Lapu City', 'Cebu', 'lapu-lapu.jpg',  1),
    ('Talisay City',   'Cebu', 'talisay.jpg',    1),
    ('Toledo City',    'Cebu', 'toledo.jpg',     1),
    ('Carcar City',    'Cebu', 'carcar.jpg',     1),
    ('Danao City',     'Cebu', 'danao.jpg',      1),
    ('Naga City',      'Cebu', 'naga.jpg',       1);

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
    cuisine VARCHAR(120) NOT NULL DEFAULT '',
    cover_image VARCHAR(255) DEFAULT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    rating_count INT NOT NULL DEFAULT 0,
    prep_min INT NOT NULL DEFAULT 20,
    prep_max INT NOT NULL DEFAULT 30,
    free_delivery TINYINT(1) NOT NULL DEFAULT 0,
    price_level TINYINT NOT NULL DEFAULT 2,
    opens_at TIME NOT NULL DEFAULT '09:00:00',
    closes_at TIME NOT NULL DEFAULT '21:00:00',
    application_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    is_open TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL,
    INDEX idx_application_status (application_status),
    INDEX idx_is_open (is_open),
    INDEX idx_city (city_id)
) ENGINE=InnoDB;

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category VARCHAR(60) NOT NULL DEFAULT 'Others',
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    INDEX idx_merchant (merchant_id),
    INDEX idx_available (is_available)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- Demo data: restaurants (merchants) + their menus, for Cebu cities
-- city_id: 1=Cebu City, 2=Mandaue City, 3=Lapu-Lapu City, 4=Talisay City
-- ----------------------------------------------------------------------------

-- Merchant owner accounts (all share demo password 'demo1234')
INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status) VALUES
    ('Owner', 'Twinniz',  'twinniz@demo.ph',   '09170000001', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Famealy',  'famealy@demo.ph',   '09170000002', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Joye',     'joye@demo.ph',      '09170000003', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'MaxChao',  'maxchao@demo.ph',   '09170000004', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Zowi',     'zowi@demo.ph',      '09170000005', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Andoks',   'andoks@demo.ph',    '09170000006', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'BMachine', 'bmachine@demo.ph',  '09170000007', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'SeaBreeze','seabreeze@demo.ph', '09170000008', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Mactan',   'mactan@demo.ph',    '09170000009', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'Island',   'island@demo.ph',    '09170000010', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'TLechon',  'tlechon@demo.ph',   '09170000011', '$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active'),
    ('Owner', 'PastaBella','pastabella@demo.ph','09170000012','$2y$10$RPGk0zcggoDOirrBBvXAa.VhsyzdSfv0n0bRwmipZhBoEd6.d0nPa', 'merchant', 'active');

INSERT INTO merchants
    (user_id, business_name, business_address, city_id, cuisine, cover_image, rating, rating_count, prep_min, prep_max, free_delivery, price_level, opens_at, closes_at, application_status, is_open) VALUES
    (1,  'Twinniz Cafe',         'Osmena Blvd, Cebu City',        1, 'Desserts, Comfort Food', 'coffee.jpg',  4.8, 472, 25, 30, 1, 2, '08:00:00', '21:00:00', 'approved', 1),
    (2,  'Famealy Restaurant',   'Mango Ave, Cebu City',          1, 'Filipino, Pasta',        'pasta.jpg',   4.5,  91, 30, 35, 1, 2, '10:00:00', '22:00:00', 'approved', 1),
    (3,  'Cups of Joye',         'Lahug, Cebu City',              1, 'Desserts, Coffee',       'coffee.jpg',  4.3,  26, 20, 25, 1, 2, '07:00:00', '20:00:00', 'approved', 1),
    (4,  'Max Chao Rice',        'Colon St, Cebu City',           1, 'Fast Food, Rice Meals',  'rice.jpg',    4.5,  36, 20, 25, 1, 1, '09:00:00', '21:00:00', 'approved', 1),
    (5,  'Zowi''s Lechon',       'Capitol Site, Cebu City',       1, 'Filipino, Grill',        'chicken.jpg', 5.0,  61, 30, 35, 1, 3, '10:00:00', '20:00:00', 'approved', 1),
    (6,  'Andoks - Mandaue',     'A.S. Fortuna St, Mandaue City', 2, 'Chicken, Filipino',      'chicken.jpg', 4.6, 188, 30, 35, 1, 2, '10:00:00', '21:00:00', 'approved', 1),
    (7,  'Burger Machine',       'Plaridel St, Mandaue City',     2, 'Burgers, Fast Food',     'burger.jpg',  4.2, 140, 15, 25, 1, 1, '00:00:00', '23:59:00', 'approved', 1),
    (8,  'Sea Breeze Grill',     'Hi-way, Mandaue City',          2, 'Seafood, Grill',         'seafood.jpg', 4.7,  54, 30, 40, 0, 3, '11:00:00', '22:00:00', 'approved', 1),
    (9,  'Mactan Seafoods',      'M.L. Quezon Ave, Lapu-Lapu',    3, 'Seafood',                'seafood.jpg', 4.9, 210, 35, 45, 0, 3, '11:00:00', '22:00:00', 'approved', 1),
    (10, 'Island Brew',          'Pajo, Lapu-Lapu City',          3, 'Coffee, Desserts',       'coffee.jpg',  4.4,  38, 20, 25, 1, 2, '07:00:00', '21:00:00', 'approved', 1),
    (11, 'Talisay Lechon House', 'Tabunok, Talisay City',         4, 'Filipino, Grill',        'chicken.jpg', 4.8, 165, 30, 35, 1, 3, '09:00:00', '20:00:00', 'approved', 1),
    (12, 'Pasta Bella',          'Lawaan, Talisay City',          4, 'Pasta, Italian',         'pasta.jpg',   4.3,  47, 25, 35, 1, 2, '10:00:00', '22:00:00', 'approved', 1);

INSERT INTO menu_items (merchant_id, name, description, price, image, category) VALUES
    -- Twinniz Cafe
    (1, 'Caramel Macchiato',    'Espresso with steamed milk and caramel drizzle', 145.00, 'coffee.jpg',  'Drinks'),
    (1, 'Classic Cheesecake',   'New York style baked cheesecake slice',          165.00, 'dessert.jpg', 'Desserts'),
    (1, 'Loaded Fries',         'Fries topped with cheese sauce and bacon',       135.00, 'fries.jpg',   'Snacks'),
    (1, 'Belgian Waffle',       'Crisp waffle with maple syrup and butter',       155.00, 'dessert.jpg', 'Desserts'),
    -- Famealy Restaurant
    (2, 'Spaghetti Famealy',    'Sweet-style Filipino spaghetti with hotdog',     159.00, 'pasta.jpg',   'Pasta'),
    (2, 'Chicken Inasal',       'Grilled marinated chicken with rice',            189.00, 'chicken.jpg', 'Filipino'),
    (2, 'Carbonara Supreme',    'Creamy carbonara with bacon and mushroom',       179.00, 'pasta.jpg',   'Pasta'),
    (2, 'Beef Caldereta',       'Hearty beef stew with potatoes and carrots',     219.00, 'rice.jpg',    'Filipino'),
    -- Cups of Joye
    (3, 'Iced Spanish Latte',   'Espresso, milk and condensed milk over ice',     135.00, 'coffee.jpg',  'Drinks'),
    (3, 'Matcha Cream',         'Premium matcha with cold foam',                  149.00, 'coffee.jpg',  'Drinks'),
    (3, 'Choco Lava Cake',      'Warm chocolate cake with molten center',         159.00, 'dessert.jpg', 'Desserts'),
    (3, 'Blueberry Muffin',     'Soft muffin loaded with blueberries',             89.00, 'dessert.jpg', 'Desserts'),
    -- Max Chao Rice
    (4, 'Chao Fan Rice',        'Wok-fried rice with egg and veggies',             99.00, 'rice.jpg',    'Rice Meals'),
    (4, 'Pork Siomai (6pc)',    'Steamed pork dumplings with chili garlic',        79.00, 'rice.jpg',    'Snacks'),
    (4, 'Beef Rice Bowl',       'Savory beef strips over garlic rice',            129.00, 'rice.jpg',    'Rice Meals'),
    (4, 'Lumpia Shanghai',      'Crispy pork spring rolls (8pc)',                  95.00, 'fries.jpg',   'Snacks'),
    -- Zowi's Lechon
    (5, 'Lechon 1/4 Kilo',      'Crispy roasted pork with liver sauce',           320.00, 'chicken.jpg', 'Filipino'),
    (5, 'Lechon Belly Roll',    'Boneless lechon belly, extra crispy',            389.00, 'chicken.jpg', 'Filipino'),
    (5, 'Dinuguan',             'Pork blood stew with puto',                      149.00, 'rice.jpg',    'Filipino'),
    (5, 'Puso Rice (3pc)',      'Hanging rice in woven coconut leaves',            45.00,  'rice.jpg',    'Rice Meals'),
    -- Andoks - Mandaue
    (6, 'Classic Lechon Manok', 'Whole roasted chicken, Andoks-style',            255.00, 'chicken.jpg', 'Chicken'),
    (6, 'Liempo Plate',         'Grilled pork belly with rice',                   165.00, 'chicken.jpg', 'Filipino'),
    (6, 'Pork BBQ (2 sticks)',  'Sweet marinated pork skewers',                    90.00, 'chicken.jpg', 'Filipino'),
    (6, 'Java Rice',            'Flavored yellow rice',                            35.00, 'rice.jpg',    'Rice Meals'),
    -- Burger Machine
    (7, 'The Big Machine',      'Quarter-pound beef burger with cheese',          129.00, 'burger.jpg',  'Burgers'),
    (7, 'Double Cheeseburger',  'Two beef patties, double cheese',                159.00, 'burger.jpg',  'Burgers'),
    (7, 'Crispy Fries',         'Golden crinkle-cut fries',                        65.00, 'fries.jpg',   'Snacks'),
    (7, 'Chicken Burger',       'Crispy chicken fillet burger',                   119.00, 'burger.jpg',  'Burgers'),
    -- Sea Breeze Grill
    (8, 'Grilled Bangus',       'Stuffed milkfish grilled to perfection',         199.00, 'seafood.jpg', 'Seafood'),
    (8, 'Garlic Buttered Shrimp','Fresh shrimp in garlic butter sauce',           289.00, 'seafood.jpg', 'Seafood'),
    (8, 'Kinilaw na Tuna',      'Fresh tuna ceviche in vinegar',                  179.00, 'seafood.jpg', 'Seafood'),
    (8, 'Seafood Boodle',       'Mixed seafood platter good for 3',               799.00, 'seafood.jpg', 'Seafood'),
    -- Mactan Seafoods
    (9, 'Sinigang na Hipon',    'Sour shrimp soup with vegetables',               249.00, 'seafood.jpg', 'Seafood'),
    (9, 'Grilled Squid',        'Charcoal-grilled squid with calamansi',          229.00, 'seafood.jpg', 'Seafood'),
    (9, 'Crab in Aligue',       'Crab cooked in fat sauce',                       459.00, 'seafood.jpg', 'Seafood'),
    (9, 'Steamed Lapu-Lapu',    'Whole grouper steamed with ginger',              549.00, 'seafood.jpg', 'Seafood'),
    -- Island Brew
    (10, 'Cold Brew',           'Slow-steeped 18-hour cold brew',                 129.00, 'coffee.jpg',  'Drinks'),
    (10, 'Caramel Frappe',      'Blended iced coffee with caramel',               155.00, 'coffee.jpg',  'Drinks'),
    (10, 'Banana Loaf',         'Moist banana bread slice',                        79.00, 'dessert.jpg', 'Desserts'),
    (10, 'Cookies (3pc)',       'Chewy chocolate chip cookies',                    99.00, 'dessert.jpg', 'Desserts'),
    -- Talisay Lechon House
    (11, 'Talisay Lechon 1/4',  'Famous Talisay-style crispy lechon',             340.00, 'chicken.jpg', 'Filipino'),
    (11, 'Lechon Sisig',        'Chopped lechon sisig, sizzling',                 189.00, 'chicken.jpg', 'Filipino'),
    (11, 'Bam-i',               'Cebuano mixed noodles',                          159.00, 'pasta.jpg',   'Filipino'),
    (11, 'Extra Rice',          'Plain steamed rice',                              25.00, 'rice.jpg',    'Rice Meals'),
    -- Pasta Bella
    (12, 'Truffle Carbonara',   'Creamy carbonara with truffle oil',              219.00, 'pasta.jpg',   'Pasta'),
    (12, 'Lasagna Bella',       'Layered beef lasagna with cheese',               239.00, 'pasta.jpg',   'Pasta'),
    (12, 'Aglio e Olio',        'Garlic and olive oil spaghetti',                 169.00, 'pasta.jpg',   'Pasta'),
    (12, 'Garlic Bread',        'Toasted bread with garlic butter',                89.00, 'fries.jpg',   'Snacks');

-- ----------------------------------------------------------------------------
-- Orders: one order per restaurant. Line items snapshot name/price so a later
-- menu change never rewrites order history.
-- ----------------------------------------------------------------------------

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_user_id INT NOT NULL,
    merchant_id INT NOT NULL,
    rider_id INT DEFAULT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    payment_method ENUM('cod') NOT NULL DEFAULT 'cod',
    subtotal DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'preparing', 'on_the_way', 'delivered', 'cancelled')
        NOT NULL DEFAULT 'pending',
    notes VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    FOREIGN KEY (rider_id) REFERENCES riders(id) ON DELETE SET NULL,
    INDEX idx_customer (customer_user_id),
    INDEX idx_merchant (merchant_id),
    INDEX idx_rider (rider_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;
