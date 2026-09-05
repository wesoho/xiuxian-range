-- ============================================================
-- 迁移 009：斗法台战绩增加逐题解析列（存量部署执行一次即可）
--
-- 交卷后刷新页面只能看到总分、看不到逐题解析，本迁移为
-- quiz_attempts 增加 score_detail（JSON），交卷时写入，
-- 重访 /doufatai 即可回看每道题的对错与解析。
-- ============================================================

ALTER TABLE `quiz_attempts` ADD COLUMN `score_detail` LONGTEXT DEFAULT NULL COMMENT '逐题解析 JSON（交卷后可回看）';
