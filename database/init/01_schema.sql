-- ============================================================
-- 修真网络安全靶场 - 数据库结构
-- XiuXian Range Database Schema v1.0
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- 1. 用户表
-- 修真弟子登记表：境界、宗门、积分、称号
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL COMMENT '修真代号（登录名）',
    `email` VARCHAR(120) DEFAULT NULL COMMENT '邮箱（可选）',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Argon2id 密码哈希',
    `sect` ENUM('qiingong','wanmozong','lunhuizong','wanderer') NOT NULL DEFAULT 'wanderer' COMMENT '所属宗门：qiingong=青云宗, wanmozong=万魔宗, lunhuizong=轮回宗, wanderer=散修',
    `realm_level` ENUM('liqi','zhuji','jindan','yuanying','huashen','lianxu','heti','dacheng') NOT NULL DEFAULT 'liqi' COMMENT '修真境界：liqi=炼气, zhuji=筑基, jindan=金丹, yuanying=元婴, huashen=化神, lianxu=炼虚, heti=合体, dacheng=大乘',
    `realm_exp` INT NOT NULL DEFAULT 0 COMMENT '当前境界经验值',
    `total_points` INT NOT NULL DEFAULT 0 COMMENT '累计修真点数',
    `title` VARCHAR(100) DEFAULT NULL COMMENT '称号（如：青云宗大师兄）',
    `role` ENUM('user','admin') NOT NULL DEFAULT 'user' COMMENT '平台角色：user=弟子, admin=长老',
    `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像URL',
    `bio` TEXT COMMENT '修真简介',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='修真弟子登记表';

-- -----------------------------------------------------------
-- 2. 关卡表
-- 100关卡元数据（境界/宗门/分类/难度）
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `challenges`;
CREATE TABLE `challenges` (
    `id` VARCHAR(20) NOT NULL COMMENT '关卡编号，如 QY-LQ-01',
    `title` VARCHAR(200) NOT NULL COMMENT '关卡标题（含修真叙事）',
    `sect` ENUM('qiingong','wanmozong','lunhuizong','wanderer') NOT NULL COMMENT '所属宗门',
    `realm` ENUM('liqi','zhuji','jindan','yuanying','huashen','lianxu','heti','dacheng') NOT NULL COMMENT '所属境界',
    `difficulty` TINYINT NOT NULL DEFAULT 1 COMMENT '难度 1-5',
    `category` VARCHAR(50) NOT NULL COMMENT '漏洞类型分类（sql/xss/csrf/...）',
    `narrative` TEXT COMMENT '剧情背景（修真叙事）',
    `description` TEXT COMMENT '关卡简介',
    `learn_content` LONGTEXT COMMENT '学习内容（Markdown）',
    `flag` VARCHAR(100) NOT NULL COMMENT '关卡 Flag（明文存储，便于自动验证）',
    `points` INT NOT NULL DEFAULT 10 COMMENT '基础修真点数',
    `order_num` INT NOT NULL DEFAULT 0 COMMENT '展示顺序',
    `prerequisites` JSON DEFAULT NULL COMMENT '前置关卡（数组）',
    `source_viewable` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否允许查看源码',
    `enabled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_realm` (`realm`),
    KEY `idx_sect` (`sect`),
    KEY `idx_category` (`category`),
    KEY `idx_difficulty` (`difficulty`),
    KEY `idx_order` (`order_num`),
    KEY `idx_enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='修真关卡（漏洞场景）';

-- -----------------------------------------------------------
-- 3. 用户进度表
-- 弟子闯关记录
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `progress`;
CREATE TABLE `progress` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `challenge_id` VARCHAR(20) NOT NULL,
    `status` ENUM('locked','unlocked','in_progress','completed') NOT NULL DEFAULT 'locked' COMMENT 'locked=未解锁, unlocked=已解锁, in_progress=试炼中, completed=已通关',
    `hints_used` JSON DEFAULT NULL COMMENT '已使用的提示编号（数组）',
    `attempts` INT NOT NULL DEFAULT 0 COMMENT '尝试次数',
    `time_spent` INT NOT NULL DEFAULT 0 COMMENT '累计耗时（秒）',
    `completed_at` DATETIME DEFAULT NULL,
    `points_earned` INT NOT NULL DEFAULT 0 COMMENT '获得点数',
    `writeup` TEXT DEFAULT NULL COMMENT '弟子自撰的解题报告',
    `started_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_challenge` (`user_id`, `challenge_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_status` (`status`),
    KEY `idx_completed` (`completed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子闯关进度';

-- -----------------------------------------------------------
-- 4. 提示表
-- 三级提示（弱提示/中等提示/完整答案）
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `hints`;
CREATE TABLE `hints` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `challenge_id` VARCHAR(20) NOT NULL,
    `level` TINYINT NOT NULL COMMENT '1=弱提示, 2=中等提示, 3=完整答案',
    `content` TEXT NOT NULL,
    `point_cost` INT NOT NULL DEFAULT 0 COMMENT '查看此提示消耗的点数',
    `order_num` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='关卡提示';

-- -----------------------------------------------------------
-- 5. 徽章表
-- 修真成就 / 解锁称号
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `badges`;
CREATE TABLE `badges` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL COMMENT '徽章名称',
    `description` TEXT COMMENT '解锁条件描述',
    `icon` VARCHAR(255) DEFAULT NULL COMMENT '图标 URL',
    `realm` ENUM('liqi','zhuji','jindan','yuanying','huashen','lianxu','heti','dacheng') DEFAULT NULL COMMENT '所属境界（NULL=不限）',
    `tier` ENUM('bronze','silver','gold','platinum','legendary') NOT NULL DEFAULT 'bronze',
    `condition` JSON DEFAULT NULL COMMENT '解锁条件 JSON',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='修真徽章';

-- -----------------------------------------------------------
-- 6. 用户徽章关联表
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `user_badges`;
CREATE TABLE `user_badges` (
    `user_id` INT UNSIGNED NOT NULL,
    `badge_id` INT UNSIGNED NOT NULL,
    `earned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `badge_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_badge` (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子已获徽章';

-- -----------------------------------------------------------
-- 7. Writeup 表
-- 弟子公开的解题报告
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `writeups`;
CREATE TABLE `writeups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `challenge_id` VARCHAR(20) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 0,
    `likes` INT NOT NULL DEFAULT 0,
    `views` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_public` (`is_public`),
    KEY `idx_likes` (`likes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子 Writeup';

-- -----------------------------------------------------------
-- 8. 关卡日志
-- 用户在关卡中的行为审计
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `challenge_logs`;
CREATE TABLE `challenge_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `challenge_id` VARCHAR(20) DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL COMMENT 'view_learn/open_challenge/submit_flag/view_hint/open_source/review',
    `detail` TEXT COMMENT '附加信息（JSON）',
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_challenge` (`challenge_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='关卡行为审计日志';

-- -----------------------------------------------------------
-- 9. 系统配置表（可选，用于动态配置）
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT,
    `description` VARCHAR(255) DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';

-- -----------------------------------------------------------
-- 10. 演示用户表（关卡内部使用）
-- 一些关卡需要预先准备的用户数据
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `demo_users`;
CREATE TABLE `demo_users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(100) NOT NULL COMMENT '明文/弱哈希，用于演示漏洞',
    `email` VARCHAR(120) DEFAULT NULL,
    `role` VARCHAR(50) DEFAULT 'user',
    `balance` DECIMAL(10,2) DEFAULT 0.00,
    `phone` VARCHAR(20) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='关卡演示用用户（与平台账号独立）';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 11. 彩蛋系统 / 趣味玩法（与 migrations/006_easter_eggs.sql 保持一致）
-- ============================================================

ALTER TABLE `users` ADD COLUMN `ascended_at` DATETIME DEFAULT NULL COMMENT '飞升时间（100关全通）';

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

CREATE TABLE IF NOT EXISTS `user_easter_eggs` (
    `user_id`   INT UNSIGNED NOT NULL,
    `egg_code`  VARCHAR(50)  NOT NULL,
    `earned_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `egg_code`),
    KEY `idx_egg_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子彩蛋收集';

CREATE TABLE IF NOT EXISTS `user_slips` (
    `user_id`   INT UNSIGNED NOT NULL,
    `slip_no`   TINYINT UNSIGNED NOT NULL COMMENT '残页编号 1-5',
    `earned_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `slip_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='天机残页收集';

CREATE TABLE IF NOT EXISTS `fortune_draws` (
    `user_id`       INT UNSIGNED NOT NULL,
    `draw_date`     DATE         NOT NULL,
    `fortune_key`   VARCHAR(50)  NOT NULL,
    `reward_points` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `draw_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='天机阁每日求签';

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

CREATE TABLE IF NOT EXISTS `user_cosmetics` (
    `user_id`       INT UNSIGNED NOT NULL,
    `cosmetic_code` VARCHAR(50)  NOT NULL,
    `equipped`      TINYINT(1)   NOT NULL DEFAULT 0,
    `acquired_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `cosmetic_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='弟子装扮';

CREATE TABLE IF NOT EXISTS `quiz_questions` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category`    VARCHAR(50)  NOT NULL DEFAULT '综合',
    `question`    TEXT         NOT NULL,
    `options`     TEXT         NOT NULL COMMENT 'JSON 数组，如 ["甲","乙","丙","丁"]',
    `answer_idx`  TINYINT      NOT NULL DEFAULT 0 COMMENT '正确选项下标（0 起）',
    `explanation` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='斗法台题库';

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
    `user_id`       INT UNSIGNED NOT NULL,
    `quiz_date`     DATE         NOT NULL,
    `score`         TINYINT      NOT NULL DEFAULT 0,
    `points_earned` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `quiz_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='斗法台每日战绩';

CREATE TABLE IF NOT EXISTS `user_bounties` (
    `user_id`     INT UNSIGNED NOT NULL,
    `bounty_date` DATE         NOT NULL,
    `bounty_key`  VARCHAR(50)  NOT NULL,
    `claimed_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `bounty_date`, `bounty_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='悬赏令领取记录';

CREATE TABLE IF NOT EXISTS `user_counters` (
    `user_id`     INT UNSIGNED NOT NULL,
    `counter_key` VARCHAR(50)  NOT NULL,
    `value`       INT          NOT NULL DEFAULT 0,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `counter_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户通用计数器';

-- ============================================================
-- 初始化完成
-- ============================================================