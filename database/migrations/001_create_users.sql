-- ============================================================
-- 修真网络安全靶场 - 数据库迁移 001：创建 users 表
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(120) DEFAULT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `sect` ENUM('qiingong','wanmozong','lunhuizong','wanderer') NOT NULL DEFAULT 'wanderer',
    `realm_level` ENUM('liqi','zhuji','jindan','yuanying','huashen','lianxu','heti','dacheng') NOT NULL DEFAULT 'liqi',
    `realm_exp` INT NOT NULL DEFAULT 0,
    `total_points` INT NOT NULL DEFAULT 0,
    `title` VARCHAR(100) DEFAULT NULL,
    `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `bio` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login_at` DATETIME DEFAULT NULL,
    `last_login_ip` VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_realm` (`realm_level`),
    KEY `idx_sect` (`sect`),
    KEY `idx_points` (`total_points`),
    KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;