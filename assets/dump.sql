CREATE DATABASE IF NOT EXISTS `waterapp`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `waterapp`;

SET NAMES utf8mb4;

-- ----------------------------
-- Table: daily_sales
-- ----------------------------
DROP TABLE IF EXISTS `daily_sales`;
CREATE TABLE `daily_sales` (
  `daily_id` int NOT NULL AUTO_INCREMENT,
  `sales_date` date DEFAULT NULL,
  `total_transactions` int DEFAULT NULL,
  `total_items_sold` int DEFAULT NULL,
  `total_revenue` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`daily_id`),
  UNIQUE KEY `sales_date` (`sales_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `daily_sales`
VALUES (2,'2025-12-30',10,33,4354.00);

-- ----------------------------
-- Table: inventory
-- ----------------------------
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inventory`
VALUES
(2,'TestProduct','testing',100.00,100),
(3,'1000ml Plastic Gallon','container',50.00,100),
(4,'Water Dispenser','machine',5000.00,50),
(5,'Water Filter','tool',200.00,50),
(6,'WebTestProduct','testing',100.00,0);

-- ----------------------------
-- Table: monthly_sales
-- ----------------------------
DROP TABLE IF EXISTS `monthly_sales`;
CREATE TABLE `monthly_sales` (
  `monthly_id` int NOT NULL AUTO_INCREMENT,
  `year` int DEFAULT NULL,
  `month` int DEFAULT NULL,
  `total_transactions` int DEFAULT NULL,
  `total_items_sold` int DEFAULT NULL,
  `total_revenue` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`monthly_id`),
  UNIQUE KEY `year_month` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: service_requests
-- ----------------------------
DROP TABLE IF EXISTS `service_requests`;
CREATE TABLE `service_requests` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `phone_no` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `service_ordered` varchar(100) NOT NULL,
  `status` enum('Pending','Done') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: transaction_items
-- ----------------------------
DROP TABLE IF EXISTS `transaction_items`;
CREATE TABLE `transaction_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: transaction_log
-- ----------------------------
DROP TABLE IF EXISTS `transaction_log`;
CREATE TABLE `transaction_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: transactions
-- ----------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: weekly_sales
-- ----------------------------
DROP TABLE IF EXISTS `weekly_sales`;
CREATE TABLE `weekly_sales` (
  `weekly_id` int NOT NULL AUTO_INCREMENT,
  `year` int DEFAULT NULL,
  `week_number` int DEFAULT NULL,
  `total_transactions` int DEFAULT NULL,
  `total_items_sold` int DEFAULT NULL,
  `total_revenue` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`weekly_id`),
  UNIQUE KEY `year_week` (`year`,`week_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
