-- ============================================================
--  RESTAURANT DATABASE SCHEMA
--  Import via phpMyAdmin: Import > pilih file ini > Go
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ------------------------------------------------------------
-- DATABASE (opsional: ganti nama sesuai kebutuhan)
-- ------------------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS `restaurant_db`
--   DEFAULT CHARACTER SET utf8mb4
--   DEFAULT COLLATE utf8mb4_unicode_ci;
-- USE `restaurant_db`;

-- ============================================================
--  USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)  DEFAULT NULL,
  `username`      VARCHAR(50)   NOT NULL,
  `password_hash` VARCHAR(255)  DEFAULT NULL,
  `role`          VARCHAR(50)   DEFAULT NULL,
  `shift`         VARCHAR(50)   DEFAULT NULL,
  `status`        VARCHAR(50)   DEFAULT 'active',
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  CATEGORIES
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  DEFAULT NULL,
  `description` TEXT          DEFAULT NULL,
  `sort_order`  INT           DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SUPPLIERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id`      INT           NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(100)  DEFAULT NULL,
  `contact` VARCHAR(100)  DEFAULT NULL,
  `address` TEXT          DEFAULT NULL,
  `notes`   TEXT          DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  MENUS
-- ============================================================
CREATE TABLE IF NOT EXISTS `menus` (
  `id`          INT             NOT NULL AUTO_INCREMENT,
  `category_id` INT             DEFAULT NULL,
  `name`        VARCHAR(100)    DEFAULT NULL,
  `description` TEXT            DEFAULT NULL,
  `price`       DECIMAL(10,2)   DEFAULT 0.00,
  `hpp`         DECIMAL(10,2)   DEFAULT 0.00,
  `status`      VARCHAR(50)     DEFAULT 'available',
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_menus_category` (`category_id`),
  CONSTRAINT `fk_menus_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  ORDERS  (dibuat sebelum tables karena tables merujuk ke orders)
-- ============================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id`           INT             NOT NULL AUTO_INCREMENT,
  `table_id`     INT             DEFAULT NULL,
  `user_id`      INT             DEFAULT NULL,
  `status`       VARCHAR(50)     DEFAULT 'open',
  `total_amount` DECIMAL(10,2)   DEFAULT 0.00,
  `notes`        TEXT            DEFAULT NULL,
  `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLES  (meja restoran)
-- ============================================================
CREATE TABLE IF NOT EXISTS `tables` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(50)  DEFAULT NULL,
  `capacity`         INT          DEFAULT NULL,
  `status`           VARCHAR(50)  DEFAULT 'available',
  `current_order_id` INT          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tables_order` (`current_order_id`),
  CONSTRAINT `fk_tables_order`
    FOREIGN KEY (`current_order_id`) REFERENCES `orders` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tambahkan FK table_id di orders -> tables (setelah tables dibuat)
-- ------------------------------------------------------------
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_table`
    FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
--  ORDER ITEMS
-- ============================================================
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT             NOT NULL AUTO_INCREMENT,
  `order_id`   INT             DEFAULT NULL,
  `menu_id`    INT             DEFAULT NULL,
  `qty`        INT             DEFAULT 1,
  `price`      DECIMAL(10,2)   DEFAULT 0.00,
  `subtotal`   DECIMAL(10,2)   DEFAULT 0.00,
  `notes`      TEXT            DEFAULT NULL,
  `status`     VARCHAR(50)     DEFAULT 'pending',
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_oi_order` (`order_id`),
  KEY `fk_oi_menu`  (`menu_id`),
  CONSTRAINT `fk_oi_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oi_menu`
    FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  PAYMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id`             INT             NOT NULL AUTO_INCREMENT,
  `order_id`       INT             DEFAULT NULL,
  `amount`         DECIMAL(10,2)   DEFAULT 0.00,
  `payment_method` VARCHAR(50)     DEFAULT NULL,
  `status`         VARCHAR(50)     DEFAULT 'pending',
  `paid_at`        TIMESTAMP       NULL DEFAULT NULL,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_payments_order` (`order_id`),
  CONSTRAINT `fk_payments_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  INVENTORY / STOCK
-- ============================================================
CREATE TABLE IF NOT EXISTS `inventory` (
  `id`          INT             NOT NULL AUTO_INCREMENT,
  `supplier_id` INT             DEFAULT NULL,
  `name`        VARCHAR(100)    DEFAULT NULL,
  `unit`        VARCHAR(30)     DEFAULT NULL,
  `stock`       DECIMAL(10,2)   DEFAULT 0.00,
  `min_stock`   DECIMAL(10,2)   DEFAULT 0.00,
  `price`       DECIMAL(10,2)   DEFAULT 0.00,
  `notes`       TEXT            DEFAULT NULL,
  `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_inventory_supplier` (`supplier_id`),
  CONSTRAINT `fk_inventory_supplier`
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  END OF SCHEMA
-- ============================================================
