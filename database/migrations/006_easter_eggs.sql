-- ============================================================
-- 迁移 006：彩蛋系统 / 趣味玩法（天机阁 / 万宝楼 / 斗法台 / 悬赏令 / 飞升）
--
-- 设计原则：
--   1. 彩蛋只发徽章/称号/装扮，不发闯关积分（飞升与玩法奖励除外，且金额极小），
--      保证排行榜公平性。
--   2. 彩蛋口令（egg flag）与正式关卡 flag 分离，存于 easter_eggs.secret。
--
-- 适用于已部署的旧库；新库由 database/init/01_schema.sql 直接建表。
-- ============================================================

-- 弟子飞升时间（全部关卡通关时写入，全站唯一荣誉）
ALTER TABLE `users` ADD COLUMN `ascended_at` DATETIME DEFAULT NULL COMMENT '飞升时间（100关全通）';

-- 彩蛋登记表：secret 为彩蛋口令（可空 = 行为触发型彩蛋）
CREATE TABLE IF NOT EXISTS `easter_eggs` (
    `code`       VARCHAR(50)  NOT NULL COMMENT '彩蛋代号，如 egg_konami',
    `name`       VARCHAR(100) NOT NULL COMMENT '彩蛋名（收集册展示）',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '彩蛋说明（获得后可见）',
    `hint`       VARCHAR(255) DEFAULT NULL COMMENT '未获得时的模糊提示',
    `icon`       VARCHAR(32)  DEFAULT '🥚' COMMENT '图标（emoji）',
    `secret`     VARCHAR(100) DEFAULT NULL COMMENT '彩蛋口令（flag{egg_xxx}），NULL=行为触发',
    `tier`       ENUM('bronze','silver','gold','legendary') NOT NULL DEFAULT 'bronze',
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`code`),
    UNIQUE KEY `uk_egg_secret` (`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='彩蛋登记表';

-- 弟子已获得的彩蛋
CREATE TABLE IF NOT EXISTS `user_easter_eggs` (
    `user_id`   INT UNSIGNED NOT NULL,
    `egg_code`  VARCHAR(50)  NOT NULL,
    `earned_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `egg_code`),
    KEY `idx_egg_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子彩蛋收集';

-- 天机残页（寻宝链收集品，集齐五张兑换【天机子】）
CREATE TABLE IF NOT EXISTS `user_slips` (
    `user_id`   INT UNSIGNED NOT NULL,
    `slip_no`   TINYINT UNSIGNED NOT NULL COMMENT '残页编号 1-5',
    `earned_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `slip_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='天机残页收集';

-- 每日求签记录（每人每天一签）
CREATE TABLE IF NOT EXISTS `fortune_draws` (
    `user_id`       INT UNSIGNED NOT NULL,
    `draw_date`     DATE         NOT NULL,
    `fortune_key`   VARCHAR(50)  NOT NULL,
    `reward_points` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `draw_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='天机阁每日求签';

-- 万宝楼装扮商品
CREATE TABLE IF NOT EXISTS `cosmetics` (
    `code`        VARCHAR(50)  NOT NULL,
    `name`        VARCHAR(100) NOT NULL,
    `type`        VARCHAR(20)  NOT NULL DEFAULT 'title' COMMENT '类型：title=头衔 theme=主题',
    `price`       INT          NOT NULL DEFAULT 0 COMMENT '售价（0=非卖品，需成就解锁）',
    `icon`        VARCHAR(32)  DEFAULT '🎫',
    `description` VARCHAR(255) DEFAULT NULL,
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='万宝楼装扮商品';

-- 弟子已购装扮（title 类型可装备，同步 users.title）
CREATE TABLE IF NOT EXISTS `user_cosmetics` (
    `user_id`       INT UNSIGNED NOT NULL,
    `cosmetic_code` VARCHAR(50)  NOT NULL,
    `equipped`      TINYINT(1)   NOT NULL DEFAULT 0,
    `acquired_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `cosmetic_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子装扮';

-- 斗法台题库（options 为 JSON 数组字符串）
CREATE TABLE IF NOT EXISTS `quiz_questions` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category`    VARCHAR(50)  NOT NULL DEFAULT '综合',
    `question`    TEXT         NOT NULL,
    `options`     TEXT         NOT NULL COMMENT 'JSON 数组，如 ["甲","乙","丙","丁"]',
    `answer_idx`  TINYINT      NOT NULL DEFAULT 0 COMMENT '正确选项下标（0 起）',
    `explanation` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='斗法台题库';

-- 斗法台每日战绩（每人每天一战）
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
    `user_id`       INT UNSIGNED NOT NULL,
    `quiz_date`     DATE         NOT NULL,
    `score`         TINYINT      NOT NULL DEFAULT 0,
    `points_earned` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `quiz_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='斗法台每日战绩';

-- 悬赏令领取记录
CREATE TABLE IF NOT EXISTS `user_bounties` (
    `user_id`     INT UNSIGNED NOT NULL,
    `bounty_date` DATE         NOT NULL,
    `bounty_key`  VARCHAR(50)  NOT NULL,
    `claimed_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `bounty_date`, `bounty_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='悬赏令领取记录';

-- 通用计数器（灵兽图鉴等）
CREATE TABLE IF NOT EXISTS `user_counters` (
    `user_id`     INT UNSIGNED NOT NULL,
    `counter_key` VARCHAR(50)  NOT NULL,
    `value`       INT          NOT NULL DEFAULT 0,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `counter_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户通用计数器';
