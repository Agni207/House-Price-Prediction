-- ============================================================
-- HousePrice AI - Database Schema
-- Database: house_price_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS `house_price_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `house_price_db`;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: predictions
-- ============================================================
CREATE TABLE IF NOT EXISTS `predictions` (
  `id`              INT(11)        NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11)        NOT NULL,
  `city`            VARCHAR(100)   NOT NULL,
  `location`        VARCHAR(150)   NOT NULL,
  `property_type`   VARCHAR(50)    NOT NULL,
  `area`            DECIMAL(10,2)  NOT NULL,
  `bedrooms`        TINYINT(2)     NOT NULL,
  `bathrooms`       TINYINT(2)     NOT NULL,
  `floors`          TINYINT(2)     NOT NULL DEFAULT 1,
  `property_age`    SMALLINT(4)    NOT NULL DEFAULT 0,
  `parking`         TINYINT(1)     NOT NULL DEFAULT 0,
  `furnishing`      VARCHAR(20)    NOT NULL DEFAULT 'Unfurnished',
  `condition_type`  VARCHAR(20)    NOT NULL DEFAULT 'Good',
  `predicted_price` DECIMAL(15,2)  NOT NULL,
  `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_predictions_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Sample demo user  (password = "Demo@1234")
-- ============================================================
INSERT INTO `users` (`name`, `email`, `password`, `created_at`) VALUES
('Demo User', 'demo@houseprice.ai',
 '$2y$12$vZw.TBSf0Fy5FdI4IzTDGe9Zo6DhkAOIYbDuZJW2D/mZ5FVFnDCVa',
 NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
