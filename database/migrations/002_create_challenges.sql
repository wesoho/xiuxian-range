-- ============================================================
-- 迁移 002：创建 challenges 表
-- ============================================================

CREATE TABLE IF NOT EXISTS `challenges` (
    `id` VARCHAR(20) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `sect` ENUM('qiingong','wanmozong','lunhuizong','wanderer') NOT NULL,
    `realm` ENUM('liqi','zhuji','jindan','yuanying','huashen','lianxu','heti','dacheng') NOT NULL,
    `difficulty` TINYINT NOT NULL DEFAULT 1,
    `category` VARCHAR(50) NOT NULL,
    `narrative` TEXT,
    `description` TEXT,
    `learn_content` LONGTEXT,
    `flag` VARCHAR(100) NOT NULL,
    `points` INT NOT NULL DEFAULT 10,
    `order_num` INT NOT NULL DEFAULT 0,
    `prerequisites` JSON DEFAULT NULL,
    `source_viewable` TINYINT(1) NOT NULL DEFAULT 1,
    `enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_realm` (`realm`),
    KEY `idx_sect` (`sect`),
    KEY `idx_category` (`category`),
    KEY `idx_difficulty` (`difficulty`),
    KEY `idx_order` (`order_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;