-- ============================================================
-- 迁移 003：创建 progress 表
-- ============================================================

CREATE TABLE IF NOT EXISTS `progress` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `challenge_id` VARCHAR(20) NOT NULL,
    `status` ENUM('locked','unlocked','in_progress','completed') NOT NULL DEFAULT 'locked',
    `hints_used` JSON DEFAULT NULL,
    `attempts` INT NOT NULL DEFAULT 0,
    `time_spent` INT NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `points_earned` INT NOT NULL DEFAULT 0,
    `writeup` TEXT DEFAULT NULL,
    `started_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_challenge` (`user_id`, `challenge_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;