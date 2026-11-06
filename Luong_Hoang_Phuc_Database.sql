CREATE DATABASE IF NOT EXISTS dao_tran_cong_anh_shop
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dao_tran_cong_anh_shop;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(20) NULL,
  address VARCHAR(255) NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL DEFAULT 'assets/product-placeholder.svg',
  price DECIMAL(12,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  specs TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id)
    REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_products_category (category_id),
  INDEX idx_products_price (price),
  INDEX idx_products_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  order_code VARCHAR(30) NOT NULL UNIQUE,
  recipient_name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  shipping_address VARCHAR(255) NOT NULL,
  note TEXT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_orders_status (status),
  INDEX idx_orders_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(180) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id)
    REFERENCES orders(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id)
    REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_items_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo accounts. Password for all three accounts: password
-- Replace these demo credentials before any public deployment.
INSERT INTO users (full_name,email,phone,address,password,role) VALUES
('Đào Trần Công Anh','admin@example.com','0900000001','TP. Hồ Chí Minh',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Nguyễn Minh Anh','minhanh@example.com','0900000002','TP. Hồ Chí Minh',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','customer'),
('Trần Quốc Bảo','quocbao@example.com','0900000003','Bình Dương',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','customer');

INSERT INTO categories (name,slug,description) VALUES
('Laptop','laptop','Laptop học tập, văn phòng và hiệu năng cao.'),
('Linh kiện máy tính','linh-kien-may-tinh','CPU, RAM, SSD và linh kiện nâng cấp.'),
('Thiết bị ngoại vi','thiet-bi-ngoai-vi','Màn hình, bàn phím, chuột và phụ kiện.');

INSERT INTO products (category_id,name,slug,description,image,price,stock,specs) VALUES
(1,'Laptop ASUS Vivobook 15','asus-vivobook-15','Laptop 15.6 inch phục vụ học tập và văn phòng.','assets/product-placeholder.svg',13990000,8,'CPU: Intel Core i5; RAM: 16GB; SSD: 512GB; Màn hình: 15.6 inch'),
(1,'Laptop Lenovo IdeaPad Slim 3','lenovo-ideapad-slim-3','Thiết kế gọn nhẹ, phù hợp sinh viên.','assets/product-placeholder.svg',12990000,7,'CPU: AMD Ryzen 5; RAM: 16GB; SSD: 512GB; Màn hình: 15.6 inch'),
(1,'Laptop Acer Aspire 7','acer-aspire-7','Laptop hiệu năng tốt cho học tập và giải trí.','assets/product-placeholder.svg',17990000,5,'CPU: Intel Core i5; RAM: 16GB; SSD: 512GB; GPU: RTX'),
(1,'Laptop HP Pavilion 14','hp-pavilion-14','Laptop văn phòng màn hình 14 inch.','assets/product-placeholder.svg',15990000,6,'CPU: Intel Core i5; RAM: 16GB; SSD: 512GB'),
(2,'RAM Kingston Fury 16GB DDR4','ram-kingston-fury-16gb','Bộ nhớ RAM 16GB dành cho máy tính tương thích.','assets/product-placeholder.svg',890000,20,'Dung lượng: 16GB; Chuẩn: DDR4'),
(2,'SSD Samsung 980 500GB','ssd-samsung-980-500gb','Ổ cứng SSD NVMe tốc độ cao.','assets/product-placeholder.svg',1290000,15,'Dung lượng: 500GB; Chuẩn: NVMe M.2'),
(2,'CPU Intel Core i5-12400F','cpu-intel-i5-12400f','Bộ xử lý Intel Core thế hệ 12.','assets/product-placeholder.svg',2990000,9,'Nhân/luồng: 6/12; Socket: LGA1700'),
(2,'Mainboard MSI PRO B760M','mainboard-msi-b760m','Bo mạch chủ cho cấu hình Intel.','assets/product-placeholder.svg',3190000,4,'Chipset: B760; Socket: LGA1700'),
(3,'Màn hình Dell 24 inch','man-hinh-dell-24','Màn hình 24 inch phục vụ làm việc.','assets/product-placeholder.svg',2890000,10,'Kích thước: 24 inch; Độ phân giải: Full HD'),
(3,'Bàn phím Logitech K380','ban-phim-logitech-k380','Bàn phím Bluetooth nhỏ gọn.','assets/product-placeholder.svg',690000,18,'Kết nối: Bluetooth; Layout: Compact'),
(3,'Chuột Logitech G102','chuot-logitech-g102','Chuột gaming có dây.','assets/product-placeholder.svg',390000,25,'DPI: tối đa 8000; Kết nối: USB'),
(3,'Tai nghe HyperX Cloud Stinger','tai-nghe-hyperx-cloud-stinger','Tai nghe gaming có micro.','assets/product-placeholder.svg',1190000,12,'Kết nối: 3.5mm; Micro: Có');

-- Five demonstration orders in four distinct statuses.
INSERT INTO orders (user_id,order_code,recipient_name,phone,shipping_address,note,total_amount,status,created_at) VALUES
(2,'DCA-2026-0001','Nguyễn Minh Anh','0900000002','TP. Hồ Chí Minh','Giao giờ hành chính',13990000,'pending',NOW() - INTERVAL 4 DAY),
(3,'DCA-2026-0002','Trần Quốc Bảo','0900000003','Bình Dương','Gọi trước khi giao',12990000,'shipping',NOW() - INTERVAL 3 DAY),
(2,'DCA-2026-0003','Nguyễn Minh Anh','0900000002','TP. Hồ Chí Minh','',890000,'completed',NOW() - INTERVAL 10 DAY),
(3,'DCA-2026-0004','Trần Quốc Bảo','0900000003','Bình Dương','',390000,'cancelled',NOW() - INTERVAL 8 DAY),
(2,'DCA-2026-0005','Nguyễn Minh Anh','0900000002','TP. Hồ Chí Minh','',4180000,'completed',NOW() - INTERVAL 6 DAY);

INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,subtotal) VALUES
(1,1,'Laptop ASUS Vivobook 15',13990000,1,13990000),
(2,2,'Laptop Lenovo IdeaPad Slim 3',12990000,1,12990000),
(3,5,'RAM Kingston Fury 16GB DDR4',890000,1,890000),
(4,11,'Chuột Logitech G102',390000,1,390000),
(5,6,'SSD Samsung 980 500GB',1290000,2,2580000),
(5,10,'Bàn phím Logitech K380',690000,1,690000),
(5,11,'Chuột Logitech G102',390000,1,390000);
