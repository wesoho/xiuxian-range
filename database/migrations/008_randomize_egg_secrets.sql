-- ============================================================
-- 迁移 008：彩蛋口令随机化（存量部署执行一次即可）
--
-- 背景：彩蛋口令（flag{egg_xxx}）原为固定字符串，掌握命名规律即可
-- 跳过寻宝链直接兑换。本迁移将全部口令型彩蛋的 secret 替换为
-- 12 位随机 hex，并同步《宗门秘史》暗格（secret_manual）中的口令。
--
-- 所有揭示点已改为 xxr_egg_secret() 动态渲染：
--   /robots.txt（残页壹）、/?dao=1（贰）、境界地图 ?tianji=1（叁）、
--   404 藏头诗（肆）、/mijing（伍）、藏经阁深层注释（翻书虫）、
--   phpinfo 隐藏变量（寻宝之眼）、JWT 样例符文（符文解者）、
--   QY-JZ-03 UNION 暗格（宗门秘史）。
--
-- 说明：可重复执行（每次都会重新随机化，已获彩蛋记录不受影响）。
-- ============================================================

UPDATE `easter_eggs`
SET `secret` = CONCAT('flag{egg_', SUBSTRING(MD5(RAND()), 1, 12), '}')
WHERE `secret` IS NOT NULL;

UPDATE `secret_manual`
SET `content` = REPLACE(
    `content`,
    'flag{egg_sect_manual}',
    (SELECT t.secret FROM (SELECT secret FROM `easter_eggs` WHERE code = 'egg_sect_secret') t)
);
