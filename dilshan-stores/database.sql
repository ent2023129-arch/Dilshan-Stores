-- ============================================================
-- Dilshan Stores - Automatic Bill Calculation System
-- Database schema
-- Import this file via phpMyAdmin, or:
--   mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS dilshan_stores
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dilshan_stores;

-- ------------------------------------------------------------
-- users : registered accounts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,          -- stored using PHP password_hash()
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- bills : one row per saved invoice
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bills (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  customer_name VARCHAR(150) NOT NULL,
  invoice_no    VARCHAR(50)  NOT NULL,
  bill_date     DATE NOT NULL,
  subtotal      DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount_amt  DECIMAL(12,2) NOT NULL DEFAULT 0,
  tax_amt       DECIMAL(12,2) NOT NULL DEFAULT 0,
  grand_total   DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- bill_items : line items belonging to a bill
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bill_items (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  bill_id   INT NOT NULL,
  item_name VARCHAR(150) NOT NULL,
  price     DECIMAL(12,2) NOT NULL,
  qty       INT NOT NULL,
  amount    DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- messages : contact form submissions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  message    TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
