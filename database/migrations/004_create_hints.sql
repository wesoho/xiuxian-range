-- ============================================================
-- 迁移 004：创建 hints 表
-- ============================================================

CREATE TABLE IF NOT EXISTS `hints` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `challenge_id` VARCHAR(20) NOT NULL,
    `level` TINYINT NOT NULL,
    `content` TEXT NOT NULL,
    `point_cost` INT NOT NULL DEFAULT 0,
    `order_num` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;