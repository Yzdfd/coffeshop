-- ============================================================
--  RESTAURANT DATABASE SCHEMA
--  Import via phpMyAdmin: Import > pilih file ini > Go
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ============================================================
--  USERS
-- ============================================================
CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) DEFAULT NULL,
  `username`      VARCHAR(50)  NOT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `role`          VARCHAR(50)  DEFAULT NULL,
  `shift`         VARCHAR(50)  DEFAULT NULL,
  `status`        VARCHAR(50)  DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  CATEGORIES
-- ============================================================
CREATE TABLE `categories` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) DEFAULT NULL,
  `description` TEXT         DEFAULT NULL,
  `sort_order`  INT          DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SUPPLIERS
-- ============================================================
CREATE TABLE `suppliers` (
  `id`      INT          NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(100) DEFAULT NULL,
  `contact` VARCHAR(100) DEFAULT NULL,
  `address` TEXT         DEFAULT NULL,
  `notes`   TEXT         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  MENUS
-- ============================================================
CREATE TABLE `menus` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `category_id` INT           DEFAULT NULL,
  `name`        VARCHAR(100)  DEFAULT NULL,
  `description` TEXT          DEFAULT NULL,
  `price`       DECIMAL(10,2) DEFAULT NULL,
  `hpp`         DECIMAL(10,2) DEFAULT NULL,
  `status`      VARCHAR(50)   DEFAULT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_menus_category` (`category_id`),
  CONSTRAINT `fk_menus_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  MENU_VARIANTS
-- ============================================================
CREATE TABLE `menu_variants` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `menu_id`    INT           DEFAULT NULL,
  `name`       VARCHAR(100)  DEFAULT NULL,
  `price_diff` DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_variants_menu` (`menu_id`),
  CONSTRAINT `fk_variants_menu`
    FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  INGREDIENTS
-- ============================================================
CREATE TABLE `ingredients` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `supplier_id` INT           DEFAULT NULL,
  `name`        VARCHAR(100)  DEFAULT NULL,
  `stock_qty`   DECIMAL(10,2) DEFAULT NULL,
  `min_stock`   DECIMAL(10,2) DEFAULT NULL,
  `unit`        VARCHAR(50)   DEFAULT NULL,
  `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ingredients_supplier` (`supplier_id`),
  CONSTRAINT `fk_ingredients_supplier`
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  RECIPES
-- ============================================================
CREATE TABLE `recipes` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `menu_id`       INT           DEFAULT NULL,
  `ingredient_id` INT           DEFAULT NULL,
  `qty_needed`    DECIMAL(10,2) DEFAULT NULL,
  `unit`          VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_recipes_menu` (`menu_id`),
  KEY `fk_recipes_ingredient` (`ingredient_id`),
  CONSTRAINT `fk_recipes_menu`
    FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`),
  CONSTRAINT `fk_recipes_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLES
--  (FK current_order_id ditambahkan via ALTER TABLE di bawah
--   karena tabel orders belum ada saat ini)
-- ============================================================
CREATE TABLE `tables` (
  `id`               INT         NOT NULL AUTO_INCREMENT,
  `number`           INT         DEFAULT NULL,
  `capacity`         INT         DEFAULT NULL,
  `status`           VARCHAR(50) DEFAULT NULL,
  `current_order_id` INT         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  ORDERS
-- ============================================================
CREATE TABLE `orders` (
  `id`         INT         NOT NULL AUTO_INCREMENT,
  `table_id`   INT         DEFAULT NULL,
  `waiter_id`  INT         DEFAULT NULL,
  `status`     VARCHAR(50) DEFAULT NULL,
  `notes`      TEXT        DEFAULT NULL,
  `ordered_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_table`  (`table_id`),
  KEY `fk_orders_waiter` (`waiter_id`),
  CONSTRAINT `fk_orders_table`
    FOREIGN KEY (`table_id`)  REFERENCES `tables` (`id`),
  CONSTRAINT `fk_orders_waiter`
    FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  ORDER_ITEMS
-- ============================================================
CREATE TABLE `order_items` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `order_id`   INT           DEFAULT NULL,
  `menu_id`    INT           DEFAULT NULL,
  `variant_id` INT           DEFAULT NULL,
  `qty`        INT           DEFAULT NULL,
  `unit_price` DECIMAL(10,2) DEFAULT NULL,
  `notes`      TEXT          DEFAULT NULL,
  `status`     VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_oi_order`   (`order_id`),
  KEY `fk_oi_menu`    (`menu_id`),
  KEY `fk_oi_variant` (`variant_id`),
  CONSTRAINT `fk_oi_order`
    FOREIGN KEY (`order_id`)   REFERENCES `orders` (`id`),
  CONSTRAINT `fk_oi_menu`
    FOREIGN KEY (`menu_id`)    REFERENCES `menus` (`id`),
  CONSTRAINT `fk_oi_variant`
    FOREIGN KEY (`variant_id`) REFERENCES `menu_variants` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TRANSACTIONS
-- ============================================================
CREATE TABLE `transactions` (
  `id`              INT           NOT NULL AUTO_INCREMENT,
  `order_id`        INT           DEFAULT NULL,
  `kasir_id`        INT           DEFAULT NULL,
  `subtotal`        DECIMAL(10,2) DEFAULT NULL,
  `tax_amount`      DECIMAL(10,2) DEFAULT NULL,
  `service_amount`  DECIMAL(10,2) DEFAULT NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT NULL,
  `total`           DECIMAL(10,2) DEFAULT NULL,
  `payment_method`  VARCHAR(50)   DEFAULT NULL,
  `status`          VARCHAR(50)   DEFAULT NULL,
  `paid_at`         TIMESTAMP     NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trx_order` (`order_id`),
  KEY `fk_trx_kasir` (`kasir_id`),
  CONSTRAINT `fk_trx_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `fk_trx_kasir`
    FOREIGN KEY (`kasir_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  STOCK_LOGS
-- ============================================================
CREATE TABLE `stock_logs` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `ingredient_id` INT           DEFAULT NULL,
  `order_id`      INT           DEFAULT NULL,
  `qty_change`    DECIMAL(10,2) DEFAULT NULL,
  `reason`        VARCHAR(255)  DEFAULT NULL,
  `logged_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sl_ingredient` (`ingredient_id`),
  KEY `fk_sl_order`      (`order_id`),
  CONSTRAINT `fk_sl_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  CONSTRAINT `fk_sl_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  ACTIVITY_LOGS
-- ============================================================
CREATE TABLE `activity_logs` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `user_id`     INT          DEFAULT NULL,
  `action`      VARCHAR(100) DEFAULT NULL,
  `description` TEXT         DEFAULT NULL,
  `ip_address`  VARCHAR(50)  DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_al_user` (`user_id`),
  CONSTRAINT `fk_al_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  PROMOS
-- ============================================================
CREATE TABLE `promos` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `code`        VARCHAR(50)   DEFAULT NULL,
  `type`        VARCHAR(50)   DEFAULT NULL,
  `value`       DECIMAL(10,2) DEFAULT NULL,
  `valid_from`  TIMESTAMP     NULL DEFAULT NULL,
  `valid_until` TIMESTAMP     NULL DEFAULT NULL,
  `status`      VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  ALTER TABLE tables — tambah FK current_order_id -> orders
--  (dilakukan setelah tabel orders sudah dibuat)
-- ============================================================
ALTER TABLE `tables`
  ADD KEY `fk_tables_current_order` (`current_order_id`),
  ADD CONSTRAINT `fk_tables_current_order`
    FOREIGN KEY (`current_order_id`) REFERENCES `orders` (`id`);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  END OF SCHEMA
-- ============================================================
