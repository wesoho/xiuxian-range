-- ============================================================
-- 修真网络安全靶场 - 种子数据
-- 包含：默认管理员 / 徽章 / 提示 / 演示用户
-- ============================================================

SET NAMES utf8mb4;

-- ===========================
-- 1. 默认管理员账号
-- 用户名：admin 密码：xxr_admin_2026
-- ===========================
-- Argon2id 哈希（生产环境重新生成）
INSERT INTO `users` (`username`, `email`, `password_hash`, `sect`, `realm_level`, `realm_exp`, `total_points`, `title`, `role`, `bio`) VALUES
('admin', 'admin@xiuxian-range.local', '$argon2id$v=19$m=65536,t=4,p=1$cncyTGFIZkdUWjRyRWlMTA$CEJ2couFKRd+QcIkrS5q3MqXnD8xsT+MdMGM5X/P8Oo',
 'wanderer', 'liqi', 0, 999999, '🏯 修真靶场长老', 'admin', '靶场管理员，镇守山门，负责维护宗门秩序');

-- 测试弟子账号（密码均为 xxr123456）
INSERT INTO `users` (`username`, `email`, `password_hash`, `sect`, `realm_level`, `realm_exp`, `total_points`, `title`, `role`, `bio`) VALUES
('qingyun', 'qy@xiuxian-range.local', '$argon2id$v=19$m=65536,t=4,p=1$NFRIQTUwdXU1MkYzc3RERQ$wTAH0ktPwcnNkt70XTparH4ubGHSGOEsz1Fa+MXQZRA',
 'qiingong', 'liqi', 0, 350, '青云宗·内门弟子', 'user', '青云宗正道修士'),
('wanmo', 'wm@xiuxian-range.local', '$argon2id$v=19$m=65536,t=4,p=1$NFRIQTUwdXU1MkYzc3RERQ$wTAH0ktPwcnNkt70XTparH4ubGHSGOEsz1Fa+MXQZRA',
 'wanmozong', 'liqi', 0, 350, '万魔宗·魔道弟子', 'user', '万魔宗魔道弟子'),
('lunhui', 'lh@xiuxian-range.local', '$argon2id$v=19$m=65536,t=4,p=1$NFRIQTUwdXU1MkYzc3RERQ$wTAH0ktPwcnNkt70XTparH4ubGHSGOEsz1Fa+MXQZRA',
 'lunhuizong', 'liqi', 0, 350, '轮回宗·轮回弟子', 'user', '轮回宗中立弟子');

-- ===========================
-- 2. 徽章（修真成就）
-- ===========================
INSERT INTO `badges` (`code`, `name`, `description`, `icon`, `realm`, `tier`, `condition`) VALUES
-- 进度型徽章
('first_step', '初入修真', '完成第一关', '/assets/badges/first_step.png', 'liqi', 'bronze', '{"challenges_completed": 1}'),
('liqi_done', '炼气圆满', '完成全部炼气期关卡', '/assets/badges/liqi_done.png', 'liqi', 'bronze', '{"challenges_in_realm": "liqi", "count": 10}'),
('zhuji_done', '筑基大成', '完成全部筑基期关卡', '/assets/badges/zhuji_done.png', 'zhuji', 'silver', '{"challenges_in_realm": "zhuji", "count": 15}'),
('jindan_done', '金丹大成', '完成全部金丹期关卡', '/assets/badges/jindan_done.png', 'jindan', 'silver', '{"challenges_in_realm": "jindan", "count": 15}'),
('yuanying_done', '元婴大成', '完成全部元婴期关卡', '/assets/badges/yuanying_done.png', 'yuanying', 'gold', '{"challenges_in_realm": "yuanying", "count": 15}'),
('huashen_done', '化神大成', '完成全部化神期关卡', '/assets/badges/huashen_done.png', 'huashen', 'gold', '{"challenges_in_realm": "huashen", "count": 15}'),
('lianxu_done', '炼虚大成', '完成全部炼虚期关卡', '/assets/badges/lianxu_done.png', 'lianxu', 'platinum', '{"challenges_in_realm": "lianxu", "count": 10}'),
('heti_done', '合体大成', '完成全部合体期关卡', '/assets/badges/heti_done.png', 'heti', 'platinum', '{"challenges_in_realm": "heti", "count": 10}'),
('dacheng_done', '大乘飞升', '完成全部大乘期关卡', '/assets/badges/dacheng.png', 'dacheng', 'legendary', '{"challenges_in_realm": "dacheng", "count": 10}'),
-- 主题型徽章
('sqli_master', 'SQL 大师', '完成全部SQL注入类关卡', '/assets/badges/sqli_master.png', NULL, 'platinum', '{"category": "sqli", "count": 15}'),
('xss_master', 'XSS 大师', '完成全部XSS类关卡', '/assets/badges/xss_master.png', NULL, 'platinum', '{"category": "xss", "count": 10}'),
('upload_master', '上传大师', '完成全部文件上传类关卡', '/assets/badges/upload_master.png', NULL, 'gold', '{"category": "upload", "count": 10}'),
('deserialize_master', '反序列化大师', '完成全部反序列化类关卡', '/assets/badges/deserialize_master.png', NULL, 'platinum', '{"category": "deserialize", "count": 8}'),
('rce_master', 'RCE 大师', '完成全部命令执行类关卡', '/assets/badges/rce_master.png', NULL, 'gold', '{"category": "rce", "count": 6}'),
-- 行为徽章
('first_blood', '一血', '作为全站第一个完成某关卡的弟子', '/assets/badges/first_blood.png', NULL, 'silver', '{"first_completion": true}'),
('no_hint', '自悟者', '完成任意10关卡且未使用任何提示', '/assets/badges/no_hint.png', NULL, 'gold', '{"no_hint_count": 10}'),
('speedster', '闪电', '10分钟内完成任意一关', '/assets/badges/speedster.png', NULL, 'bronze', '{"speed_run_minutes": 10}');

-- ===========================
-- 3. 演示用户表（关卡内部使用，与平台账号独立）
-- ===========================
INSERT INTO `demo_users` (`username`, `password`, `email`, `role`, `balance`, `phone`) VALUES
-- 弱口令关卡
('admin', 'admin', 'admin@example.com', 'admin', 10000.00, '13800138000'),
('user1', '123456', 'user1@example.com', 'user', 100.00, '13800138001'),
('guest', 'guest', 'guest@example.com', 'user', 50.00, NULL),
-- 业务逻辑关卡
('zhangsan', '123456', 'zhangsan@example.com', 'user', 1000.00, '13900139001'),
('lisi', '123456', 'lisi@example.com', 'user', 500.00, '13900139002'),
('wangwu', '123456', 'wangwu@example.com', 'user', 200.00, '13900139003'),
-- 演示管理员
('root', 'root', 'root@example.com', 'admin', 99999.99, NULL),
('test', 'test123', 'test@example.com', 'user', 0.00, NULL),
('demo', 'demo', 'demo@example.com', 'user', 100.00, NULL);

-- ===========================
-- 4. 提示数据（每个关卡 3 个提示）
-- ===========================

-- QY-LQ-01 HTML 注释泄露
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-LQ-01', 1, '查看页面源代码，留意 HTML 注释标记。', 0, 1),
('QY-LQ-01', 2, '在浏览器中按 Ctrl+U（或右键 → 查看页面源代码），搜索 <!-- 注释内容 -->', 2, 2),
('QY-LQ-01', 3, '直接访问 /public/challenges/qy_lq_01/ 目录或在源码注释中寻找 flag{html_comment_leak_01}', 0, 3);

-- QY-LQ-02 robots.txt
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-LQ-02', 1, '网站会告诉搜索引擎哪些路径不希望被收录。', 0, 1),
('QY-LQ-02', 2, '直接访问 /robots.txt 即可看到 Disallow 路径', 2, 2),
('QY-LQ-02', 3, '访问 /robots.txt 后查看 Disallow 路径下的文件即可找到Flag', 0, 3);

-- QY-LQ-03 .git 泄露
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-LQ-03', 1, '.git 目录存储了版本控制信息，可能泄露源码。', 0, 1),
('QY-LQ-03', 2, '尝试访问 /.git/HEAD 等常见路径', 2, 2),
('QY-LQ-03', 3, '使用工具如 git-dumper 下载 .git 目录后查看源码', 0, 3);

-- QY-LQ-04 www.zip 备份
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-LQ-04', 1, '整站备份的压缩文件通常会被遗忘在 web 根目录。', 0, 1),
('QY-LQ-04', 2, '尝试访问 /www.zip、/backup.zip、/site.tar.gz 等常见备份文件名', 2, 2),
('QY-LQ-04', 3, '访问 /www.zip 下载压缩包，解压后查看源码可获得Flag', 0, 3);

-- QY-LQ-05 phpinfo
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-LQ-05', 1, 'phpinfo() 函数会输出 PHP 的所有配置信息。', 0, 1),
('QY-LQ-05', 2, '直接访问 /phpinfo.php', 2, 2),
('QY-LQ-05', 3, '在 phpinfo 页面 Ctrl+F 搜索 flag 关键字', 0, 3);

-- LH-LQ-06 弱口令
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('LH-LQ-06', 1, '猜测最常见的用户名和密码组合。', 0, 1),
('LH-LQ-06', 2, '尝试 admin/admin, admin/123456, root/root 等', 2, 2),
('LH-LQ-06', 3, '用户名 admin，密码 admin 即可登录', 0, 3);

-- LH-LQ-07 JS前端校验
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('LH-LQ-07', 1, '前端校验只是给普通用户看的，绕过即可。', 0, 1),
('LH-LQ-07', 2, '禁用浏览器的 JavaScript，或使用 Burp Suite 改包', 2, 2),
('LH-LQ-07', 3, '使用浏览器开发者工具禁用 JS 后直接提交表单', 0, 3);

-- LH-LQ-08 HTTP响应头
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('LH-LQ-08', 1, 'HTTP 响应头中可能藏着敏感信息。', 0, 1),
('LH-LQ-08', 2, '使用浏览器开发者工具（F12）查看 Response Headers', 2, 2),
('LH-LQ-08', 3, '在 Network 标签查看响应头中的自定义字段', 0, 3);

-- WM-LQ-09 SQL错误回显
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('WM-LQ-09', 1, 'SQL 错误信息会被直接显示出来。', 0, 1),
('WM-LQ-09', 2, '输入单引号触发 SQL 错误', 2, 2),
('WM-LQ-09', 3, '在 id 参数后加单引号 '' 即可看到错误信息与Flag', 0, 3);

-- WM-LQ-10 默认配置
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('WM-LQ-10', 1, '许多系统有默认开放的配置或管理界面。', 0, 1),
('WM-LQ-10', 2, '尝试 /admin、/manager、/actuator 等常见路径', 2, 2),
('WM-LQ-10', 3, '访问 /admin/index.php 等默认管理路径即可获得Flag', 0, 3);

-- ===========================
-- 5. 系统配置
-- ===========================
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('site_name', '修真网络安全靶场', '站点名称'),
('site_slogan', '从炼气到大乘，一路修真一路飞升', '站点副标题'),
('hint_cost_h1', '0', '弱提示消耗点数'),
('hint_cost_h2', '5', '中等提示消耗点数'),
('hint_cost_h3', '15', '完整答案消耗点数'),
('register_enabled', '1', '是否开放注册'),
('default_sect', 'wanderer', '新用户默认宗门'),
('promotion_exp_base', '100', '境界升级所需基础经验值'),
('leaderboard_size', '20', '排行榜显示人数'),
('announcement', '🏯 修真靶场 v1.0 已上线！欢迎各位道友入山修炼！', '站点公告');

-- ============================================================
-- 3. 全部 100 关卡元数据（炼气→大乘，与 database/seeds/02_challenges.sql 保持同步）
-- ============================================================

-- ============================================================
-- 修真网络安全靶场 - 全部 100 关卡元数据
-- 修真八阶 × 三大宗门
-- 文件较大，但作为单文件便于一次性导入
-- 后续可拆分为多文件单独加载
-- ============================================================


-- ===========================
-- 第一阶段：炼气期 (L1 入门) - 10关
-- 主题：建立安全意识
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('QY-LQ-01', '【青云宗·炼气】藏经阁的注释', 'qiingong', 'liqi', 1, 'info_leak',
 '你刚拜入青云宗，掌门让你去藏经阁整理典籍。在翻阅一本《入门心法》时，你发现网页源码的HTML注释中似乎藏着什么...',
 '藏经阁的网页源码中有意外信息泄露。请查看页面源代码（Ctrl+U），在HTML注释中找到隐藏的Flag。',
 'flag{html_comment_leak_01}', 10, 1, 1, 1),
('QY-LQ-02', '【青云宗·炼气】守山神兽的指引', 'qiingong', 'liqi', 1, 'info_leak',
 '守山神兽不让你过，但据说在它的指引下，访客能找到通过山门的小路。',
 '有些网站会在 robots.txt 中声明不希望被搜索引擎收录的路径。请访问 /robots.txt 找到Flag。',
 'flag{robots_disallow_path_02}', 10, 2, 1, 1),
('QY-LQ-03', '【青云宗·炼气】祖师的Git事故', 'qiingong', 'liqi', 1, 'info_leak',
 '祖师爷曾把毕生所学放在一个版本控制系统中，但忘记清理。',
 '开发者将 .git 目录部署到线上，导致源码可被还原。请尝试访问 /.git/ 目录获取Flag。',
 'flag{git_directory_exposed_03}', 10, 3, 1, 1),
('QY-LQ-04', '【青云宗·炼气】整站打包泄露', 'qiingong', 'liqi', 1, 'info_leak',
 '门派管理疏忽，把整站备份压缩包放到了 webroot 下，被弟子们发现了。',
 '常见的备份文件如 www.zip / backup.zip / site.tar.gz 等会直接暴露源码。请访问 /www.zip 下载并查看。',
 'flag{backup_archive_leak_04}', 10, 4, 1, 1),
('QY-LQ-05', '【青云宗·炼气】丹房密报', 'qiingong', 'liqi', 1, 'info_leak',
 '丹房的某位师兄留下了一份详细的服务器配置单，意外地暴露在了公开路径。',
 'phpinfo() 页面会泄露服务器环境、PHP配置、敏感环境变量等信息。请访问 /phpinfo.php 寻找Flag。',
 'flag{phpinfo_disclosure_05}', 10, 5, 1, 1),
('LH-LQ-06', '【轮回宗·炼气】最弱口令', 'lunhuizong', 'liqi', 1, 'weak_password',
 '轮回宗入门考验：轮回殿门口有守卫，据说用最简单的口令就能通过。',
 '演示弱口令登录。常见弱口令：admin/admin, admin/123456, root/root 等。',
 'flag{weak_default_password_06}', 10, 6, 1, 1),
('LH-LQ-07', '【轮回宗·炼气】幻象结界', 'lunhuizong', 'liqi', 1, 'client_validate',
 '轮回宗设有幻象结界，所有验证都在前端完成，请绕过后端直接突破。',
 '前端 JavaScript 校验可以被轻易绕过。绕过前端长度/格式校验直接提交数据。',
 'flag{bypass_js_validation_07}', 10, 7, 1, 1),
('LH-LQ-08', '【轮回宗·炼气】忘川河的回声', 'lunhuizong', 'liqi', 1, 'info_leak',
 '忘川河会反射一切，河面（HTTP响应头）下藏着秘密。',
 'HTTP 响应头可能泄露服务器信息（Server、X-Powered-By）。请用浏览器开发者工具查看 Response Headers。',
 'flag{response_header_leak_08}', 10, 8, 1, 1),
('WM-LQ-09', '【万魔宗·炼气】血池的回响', 'wanmozong', 'liqi', 1, 'sqli_error',
 '万魔宗的血池会回响一切错误。当你失误时，错误信息会把所有秘密都说出来。',
 'SQL 错误回显暴露数据库信息。利用错误注入或故意触发错误读取 SQL 语句。',
 'flag{sql_error_disclosure_09}', 10, 9, 1, 1),
('WM-LQ-10', '【万魔宗·炼气】魔窟的默认禁地', 'wanmozong', 'liqi', 1, 'misconfig',
 '魔窟深处有一个默认开放的禁地，所有闯入者皆可长驱直入。',
 '常见的默认配置漏洞：Swagger UI、Actuator、phpinfo、管理后台默认路径等。请访问默认管理路径。',
 'flag{default_admin_exposed_10}', 10, 10, 1, 1);

-- ===========================
-- 第二阶段：筑基期 (L2 初级) - 15关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('QY-JZ-01', '【青云宗·筑基】练功房的咒语', 'qiingong', 'zhuji', 2, 'xss_reflected',
 '练功房的墙上刻着前辈的咒语，你输入的话会被原封不动地回显出来。',
 '反射型 XSS 入门。Payload: <script>alert(1)</script>',
 'flag{xss_reflected_basic_11}', 15, 11, 1, 1),
('QY-JZ-02', '【青云宗·筑基】转账幻阵', 'qiingong', 'zhuji', 2, 'csrf_get',
 '你发现只要把转账链接告诉别人，他们的钱就会自动转入你账户。',
 'GET 型 CSRF。利用 <img src="转账URL"> 自动发送请求。',
 'flag{csrf_get_transfer_12}', 15, 12, 1, 1),
('QY-JZ-03', '【青云宗·筑基】丹房的数字谜题', 'qiingong', 'zhuji', 2, 'sqli_numeric',
 '丹房有座石碑会显示丹药品级，输入丹方编号就能查看详情。你怀疑可以越权查看所有丹方。',
 '数字型 SQL 注入。Payload: 1 OR 1=1',
 'flag{sqli_numeric_or_13}', 15, 13, 1, 1),
('QY-JZ-04', '【青云宗·筑基】丹方的字符咒语', 'qiingong', 'zhuji', 2, 'sqli_string',
 '这次丹方名称是字符串，需要闭合引号才能注入。',
 '字符型 SQL 注入。Payload: xxx'' OR ''1''=''1',
 'flag{sqli_string_quote_14}', 15, 14, 1, 1),
('LH-JZ-05', '【轮回宗·筑基】联合试炼', 'lunhuizong', 'zhuji', 2, 'sqli_union',
 '轮回宗的试炼需要你用 UNION 联结两个查询结果。',
 'UNION 联合注入。Payload: 1'' UNION SELECT 1,2,3-- -',
 'flag{sqli_union_select_15}', 15, 15, 1, 1),
('LH-JZ-06', '【轮回宗·筑基】幽冥报错', 'lunhuizong', 'zhuji', 2, 'sqli_error',
 '幽冥之地会把一切错误放大，让你看清 SQL 语句。',
 '报错注入 extractvalue/updatexml。Payload: 1'' AND extractvalue(1,concat(0x7e,version()))-- -',
 'flag{sqli_error_extract_16}', 15, 16, 1, 1),
('LH-JZ-07', '【轮回宗·筑基】真言之试', 'lunhuizong', 'zhuji', 2, 'sqli_bool',
 '轮回殿只回应真假两种答复，你需要用真假来推断秘密。',
 '布尔盲注。利用条件表达式通过页面真假判断数据。',
 'flag{sqli_boolean_blind_17}', 15, 17, 1, 1),
('WM-JZ-08', '【万魔宗·筑基】时光咒', 'wanmozong', 'zhuji', 2, 'sqli_time',
 '万魔宗有时会用时光咒让一切停摆。利用这种停顿来推断秘密。',
 '时间盲注。Payload: 1'' AND SLEEP(5)-- -',
 'flag{sqli_time_blind_18}', 15, 18, 1, 1),
('WM-JZ-09', '【万魔宗·筑基】Ping 测灵根', 'wanmozong', 'zhuji', 2, 'rce_basic',
 '魔窟的测灵阵会根据你输入的 IP 来 ping 你，但可不止 ping 那么简单。',
 '命令注入基础。Payload: 127.0.0.1; ls /',
 'flag{rce_command_injection_19}', 15, 19, 1, 1),
('WM-JZ-10', '【万魔宗·筑基】魔影传书', 'wanmozong', 'zhuji', 2, 'csrf_post',
 '万魔宗的弟子可以不知不觉地替别人提交表单。',
 'POST 型 CSRF。利用自动提交表单。',
 'flag{csrf_post_form_20}', 15, 20, 1, 1),
('QY-JZ-11', '【青云宗·筑基】传送门的诡计', 'qiingong', 'zhuji', 2, 'open_redirect',
 '青云宗有一个传送门会跳转到任意地方。',
 'URL 重定向漏洞。Payload: ?url=http://evil.com',
 'flag{open_redirect_url_21}', 15, 21, 1, 1),
('QY-JZ-12', '【青云宗·筑基】留言板的诅咒', 'qiingong', 'zhuji', 2, 'xss_stored',
 '留言板的咒语会被永久记住，伤害所有访问者。',
 '存储型 XSS。留言内容会被持久存储。',
 'flag{xss_stored_persistent_22}', 15, 22, 1, 1),
('LH-JZ-13', '【轮回宗·筑基】忘川河底的秘密', 'lunhuizong', 'zhuji', 2, 'file_read',
 '忘川河底沉睡着历代轮回宗主的秘密，你可以潜入读取。',
 '文件下载/读取漏洞。Payload: ?file=../../../etc/passwd',
 'flag{file_read_traversal_23}', 15, 23, 1, 1),
('LH-JZ-14', '【轮回宗·筑基】上传心法', 'lunhuizong', 'zhuji', 2, 'upload_js',
 '轮回宗上传心法时只在前端检查格式。',
 '文件上传 - JS前端校验绕过。',
 'flag{upload_bypass_js_24}', 15, 24, 1, 1),
('WM-JZ-15', '【万魔宗·筑基】无形之框', 'wanmozong', 'zhuji', 2, 'clickjacking',
 '万魔宗用一个看不见的框罩住点击按钮，劫持用户操作。',
 '点击劫持 Clickjacking。利用 iframe + CSS 覆盖。',
 'flag{clickjacking_iframe_25}', 15, 25, 1, 1);

-- ===========================
-- 第三阶段：金丹期 (L3 中级) - 15关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('QY-JD-01', '【青云宗·金丹】金光的过滤', 'qiingong', 'jindan', 3, 'xss_filter',
 '金丹期的咒语会过滤一些关键字，但你可以用编码绕过。',
 'XSS HTML 实体编码绕过。',
 'flag{xss_html_entity_26}', 25, 26, 1, 1),
('QY-JD-02', '【青云宗·金丹】咒语变形', 'qiingong', 'jindan', 3, 'xss_bypass',
 '金丹真人会过滤 script 等关键字，你需要变形绕过。',
 'XSS 关键字过滤绕过（大小写、双写、嵌套）。',
 'flag{xss_keyword_bypass_27}', 25, 27, 1, 1),
('QY-JD-03', '【青云宗·金丹】令牌之谜', 'qiingong', 'jindan', 3, 'csrf_token',
 'CSRF Token 可以被预测或泄露。',
 'CSRF Token 可预测/泄露利用。',
 'flag{csrf_token_weak_28}', 25, 28, 1, 1),
('LH-JD-04', '【轮回宗·金丹】轮回双咒', 'lunhuizong', 'jindan', 3, 'sqli_stacked',
 '轮回宗允许同时执行多个咒语。',
 '堆叠注入。Payload: 1''; SELECT * FROM users-- -',
 'flag{sqli_stacked_query_29}', 25, 29, 1, 1),
('LH-JD-05', '【轮回宗·金丹】宽字节迷阵', 'lunhuizong', 'jindan', 3, 'sqli_gbk',
 '轮回宗使用 GBK 编码，引号会被吞掉。',
 '宽字节注入。Payload: 1%bf%27 OR 1=1-- -',
 'flag{sqli_gbk_wide_30}', 25, 30, 1, 1),
('LH-JD-06', '【轮回宗·金丹】二次重生', 'lunhuizong', 'jindan', 3, 'sqli_second',
 '轮回宗会让你重生于第二次注册时。',
 '二次注入。先注册恶意用户名，再触发查询。',
 'flag{sqli_second_order_31}', 25, 31, 1, 1),
('WM-JD-07', '【万魔宗·金丹】禁咒过滤', 'wanmozong', 'jindan', 3, 'sqli_filter',
 '万魔宗过滤了 union/select 等关键字。',
 'SQL 注入关键字过滤绕过（双写、内联注释、hex编码）。',
 'flag{sqli_filter_bypass_32}', 25, 32, 1, 1),
('WM-JD-08', '【万魔宗·金丹】护山结界', 'wanmozong', 'jindan', 3, 'sqli_waf',
 '万魔宗的山门有护山大阵（WAF）阻挡入侵。',
 'SQL 注入 WAF 绕过（大小写、注释符、特殊字符）。',
 'flag{sqli_waf_bypass_33}', 25, 33, 1, 1),
('QY-JD-09', '【青云宗·金丹】空间的缝隙', 'qiingong', 'jindan', 3, 'rce_space',
 '青云宗过滤了空格，你可以利用其他字符代替。',
 '命令注入 - 空格过滤绕过。$IFS、%09、{cat,/etc/passwd}',
 'flag{rce_space_bypass_34}', 25, 34, 1, 1),
('QY-JD-10', '【青云宗·金丹】禁咒搜寻', 'qiingong', 'jindan', 3, 'rce_filter',
 '金丹期过滤了 cat 等关键字。',
 '命令注入 - 关键字过滤绕过（拼接、通配符）。',
 'flag{rce_keyword_filter_35}', 25, 35, 1, 1),
('LH-JD-11', '【轮回宗·金丹】轮回之眼', 'lunhuizong', 'jindan', 3, 'lfi_basic',
 '轮回宗的眼睛能看到任何文件路径。',
 '文件包含 LFI 基础。Payload: ?page=../../etc/passwd',
 'flag{lfi_path_traversal_36}', 25, 36, 1, 1),
('LH-JD-12', '【轮回宗·金丹】PHP之源', 'lunhuizong', 'jindan', 3, 'lfi_filter',
 '轮回宗用 PHP 伪协议读取源码。',
 '文件包含 php://filter 读源码。Payload: php://filter/convert.base64-encode/resource=index.php',
 'flag{lfi_php_filter_37}', 25, 37, 1, 1),
('WM-JD-13', '【万魔宗·金丹】灵识伪装', 'wanmozong', 'jindan', 3, 'upload_mime',
 '万魔宗灵识伪装术：上传时只检查 MIME 类型。',
 '文件上传 - MIME 类型绕过。',
 'flag{upload_mime_bypass_38}', 25, 38, 1, 1),
('WM-JD-14', '【万魔宗·金丹】禁咒文件', 'wanmozong', 'jindan', 3, 'upload_ext',
 '黑名单过滤可被特殊后缀绕过。',
 '文件上传 - 黑名单绕过 (.php5/.phtml/.phar/.htaccess)。',
 'flag{upload_blacklist_39}', 25, 39, 1, 1),
('QY-JD-15', '【青云宗·金丹】金身绘像', 'qiingong', 'jindan', 3, 'upload_image',
 '青云宗只接受图片，但实际上可以藏入 PHP 代码。',
 '文件上传 - 图片马与 getimagesize 绕过。',
 'flag{upload_image_horse_40}', 25, 40, 1, 1);

-- ===========================
-- 第四阶段：元婴期 (L4 高级) - 15关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('LH-YY-01', '【轮回宗·元婴】DOM幻象', 'lunhuizong', 'yuanying', 4, 'xss_dom',
 '轮回宗在客户端 DOM 中动态渲染，存在 DOM XSS。',
 'DOM 型 XSS。通过修改 URL fragment 触发。',
 'flag{xss_dom_type_41}', 35, 41, 1, 1),
('LH-YY-02', '【轮回宗·元婴】盗取灵识', 'lunhuizong', 'yuanying', 4, 'xss_cookie',
 '通过 XSS 偷取其他弟子的灵识 Cookie。',
 'XSS + Cookie 窃取（教学演示，使用本地接收端）。',
 'flag{xss_cookie_steal_42}', 35, 42, 1, 1),
('WM-YY-03', '【万魔宗·元婴】魔影重重', 'wanmozong', 'yuanying', 4, 'xxe_file',
 '万魔宗弟子可以解析外部实体读取文件。',
 'XXE 文件读取。Payload: <!ENTITY xxe SYSTEM "file:///etc/passwd">',
 'flag{xxe_file_read_43}', 35, 43, 1, 1),
('WM-YY-04', '【万魔宗·元婴】内网探秘', 'wanmozong', 'yuanying', 4, 'xxe_ssrf',
 '万魔宗通过 XXE 探测内网。',
 'XXE 内网探测。',
 'flag{xxe_ssrf_44}', 35, 44, 1, 1),
('QY-YY-05', '【青云宗·元婴】元神出窍', 'qiingong', 'yuanying', 4, 'ssrf_basic',
 '元婴期可以元神出窍，访问内网。',
 'SSRF 基础。访问 file://, gopher://, dict:// 等。',
 'flag{ssrf_basic_45}', 35, 45, 1, 1),
('QY-YY-06', '【青云宗·元婴】法器协议', 'qiingong', 'yuanying', 4, 'ssrf_protocol',
 '利用 gopher 协议攻击内网 Redis。',
 'SSRF 协议利用（gopher://）。',
 'flag{ssrf_gopher_redis_46}', 35, 46, 1, 1),
('LH-YY-07', '【轮回宗·元婴】轮回转世', 'lunhuizong', 'yuanying', 4, 'ssrf_rebind',
 '轮回宗可以让你的元神反复轮回，绕过域名绑定。',
 'SSRF DNS rebinding 绕过。',
 'flag{ssrf_dns_rebind_47}', 35, 47, 1, 1),
('LH-YY-08', '【轮回宗·元婴】反向召唤', 'lunhuizong', 'yuanying', 4, 'deserialize_wakeup',
 '万魔宗的召唤阵会被恶意构造的召唤物反向触发。',
 '反序列化 __wakeup 漏洞。',
 'flag{deserialize_wakeup_48}', 35, 48, 1, 1),
('WM-YY-09', '【万魔宗·元婴】魔器链', 'wanmozong', 'yuanying', 4, 'deserialize_pop',
 '万魔宗通过 POP 链串联多个魔法器具。',
 '反序列化 POP 链构造。',
 'flag{deserialize_pop_chain_49}', 35, 49, 1, 1),
('WM-YY-10', '【万魔宗·元婴】借物偷看', 'wanmozong', 'yuanying', 4, 'idor_horizontal',
 '万魔宗弟子可以查看其他弟子的信息。',
 '水平越权（IDOR）。修改 ID 参数访问他人数据。',
 'flag{idor_horizontal_50}', 35, 50, 1, 1),
('QY-YY-11', '【青云宗·元婴】跨界探访', 'qiingong', 'yuanying', 4, 'idor_vertical',
 '普通弟子可以闯入长老禁地。',
 '垂直越权。未鉴权访问管理后台。',
 'flag{idor_vertical_51}', 35, 51, 1, 1),
('QY-YY-12', '【青云宗·元婴】灵石篡改', 'qiingong', 'yuanying', 4, 'payment_tamper',
 '灵石交易可被篡改金额。',
 '支付漏洞 - 金额篡改。',
 'flag{payment_amount_tamper_52}', 35, 52, 1, 1),
('LH-YY-13', '【轮回宗·元婴】轮回符复用', 'lunhuizong', 'yuanying', 4, 'captcha_reuse',
 '轮回宗的验证符可以重复使用。',
 '验证码重用/不过期绕过。',
 'flag{captcha_reuse_53}', 35, 53, 1, 1),
('LH-YY-14', '【轮回宗·元婴】强行改命', 'lunhuizong', 'yuanying', 4, 'password_reset',
 '轮回宗可以强行修改他人的命运（密码）。',
 '任意密码重置漏洞。',
 'flag{password_reset_arbitrary_54}', 35, 54, 1, 1),
('WM-YY-15', '【万魔宗·元婴】魔锤试炼', 'wanmozong', 'yuanying', 4, 'brute_force',
 '万魔宗大门无任何限制，可以无限尝试。',
 '暴力破解 - 无锁定机制。',
 'flag{brute_force_no_lock_55}', 35, 55, 1, 1);

-- ===========================
-- 第五阶段：化神期 (L5 专家) - 15关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('WM-HS-01', '【万魔宗·化神】无相法印', 'wanmozong', 'huashen', 5, 'jwt_none',
 '化神期可用无相法印（alg=none）伪造身份。',
 'JWT alg:none 攻击。',
 'flag{jwt_alg_none_56}', 50, 56, 1, 1),
('WM-HS-02', '【万魔宗·化神】密钥爆破', 'wanmozong', 'huashen', 5, 'jwt_weak',
 '万魔宗密钥太弱，可被爆破。',
 'JWT 弱密钥爆破（hashcat/john）。',
 'flag{jwt_weak_secret_57}', 50, 57, 1, 1),
('QY-HS-03', '【青云宗·化神】kid 注入', 'qiingong', 'huashen', 5, 'jwt_kid',
 '青云宗令牌中的 kid 字段可注入。',
 'JWT kid 注入（SQL/路径穿越）。',
 'flag{jwt_kid_inject_58}', 50, 58, 1, 1),
('QY-HS-04', '【青云宗·化神】夺舍重生', 'qiingong', 'huashen', 5, 'oauth_redirect',
 'OAuth 回调 redirect_uri 可被劫持。',
 'OAuth redirect_uri 劫持。',
 'flag{oauth_redirect_hijack_59}', 50, 59, 1, 1),
('LH-HS-05', '【轮回宗·化神】跨界之门', 'lunhuizong', 'huashen', 5, 'cors',
 '轮回宗之门对所有域开放。',
 'CORS 配置错误利用。',
 'flag{cors_misconfig_60}', 50, 60, 1, 1),
('LH-HS-06', '【轮回宗·化神】轮回令牌', 'lunhuizong', 'huashen', 5, 'csrf_token_bypass',
 'CSRF Token 与 Session 绑定缺陷。',
 'CSRF Token 绑定绕过。',
 'flag{csrf_token_binding_61}', 50, 61, 1, 1),
('WM-HS-07', '【万魔宗·化神】魔影分流', 'wanmozong', 'huashen', 5, 'http_smuggle',
 '万魔宗在多服务器间传递请求时可被走私。',
 'HTTP 请求走私（CL-TE / TE-CL）。',
 'flag{http_smuggling_62}', 50, 62, 1, 1),
('WM-HS-08', '【万魔宗·化神】缓存幻影', 'wanmozong', 'huashen', 5, 'cache_poison',
 '万魔宗的缓存会被错误污染。',
 'Web 缓存欺骗。',
 'flag{cache_poisoning_63}', 50, 63, 1, 1),
('QY-HS-09', '【青云宗·化神】古典加密', 'qiingong', 'huashen', 5, 'crypto_ecb',
 '青云宗使用 ECB 模式加密，加密等同于明文块重排。',
 '密码学 - AES-ECB 模式利用。',
 'flag{crypto_ecb_mode_64}', 50, 64, 1, 1),
('QY-HS-10', '【青云宗·化神】哈希延展', 'qiingong', 'huashen', 5, 'crypto_hash_ext',
 '青云宗的密钥哈希可被长度扩展攻击。',
 '密码学 - Hash 长度扩展攻击。',
 'flag{crypto_hash_extend_65}', 50, 65, 1, 1),
('LH-HS-11', '【轮回宗·化神】phar 反噬', 'lunhuizong', 'huashen', 5, 'deserialize_phar',
 '轮回宗的 phar 包会被反序列化触发。',
 'Phar 反序列化利用。',
 'flag{deserialize_phar_66}', 50, 66, 1, 1),
('LH-HS-12', '【轮回宗·化神】session 反转', 'lunhuizong', 'huashen', 5, 'deserialize_session',
 'Session 序列化处理器的差异导致反序列化。',
 'Session 反序列化漏洞。',
 'flag{deserialize_session_67}', 50, 67, 1, 1),
('WM-HS-13', '【万魔宗·化神】弱类型幻象', 'wanmozong', 'huashen', 5, 'php_type_juggle',
 '万魔宗用宽松比较引你入瓮。',
 'PHP 弱类型比较绕过。',
 'flag{php_type_juggling_68}', 50, 68, 1, 1),
('WM-HS-14', '【万魔宗·化神】变量覆盖', 'wanmozong', 'huashen', 5, 'php_variable',
 '万魔宗用 extract() 覆盖你的关键变量。',
 'PHP extract()/parse_str() 变量覆盖。',
 'flag{php_variable_override_69}', 50, 69, 1, 1),
('QY-HS-15', '【青云宗·化神】in_array 陷阱', 'qiingong', 'huashen', 5, 'php_in_array',
 'in_array 第三个参数默认为 false，导致类型转换绕过。',
 'PHP in_array 弱比较绕过。',
 'flag{php_in_array_bypass_70}', 50, 70, 1, 1);

-- ===========================
-- 第六阶段：炼虚期 (L5 综合) - 10关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('QY-LX-01', '【青云宗·炼虚】日志成魔', 'qiingong', 'lianxu', 5, 'lfi_log_poison',
 '炼虚期可通过污染日志配合文件包含RCE。',
 '日志投毒 + 文件包含 RCE。',
 'flag{lfi_log_poison_71}', 60, 71, 1, 1),
('LH-LX-02', '【轮回宗·炼虚】轮回之袋', 'lunhuizong', 'lianxu', 5, 'lfi_session',
 '轮回宗的 Session 文件可被包含。',
 'Session 文件包含。',
 'flag{lfi_session_include_72}', 60, 72, 1, 1),
('WM-LX-03', '【万魔宗·炼虚】SQL通幽', 'wanmozong', 'lianxu', 5, 'sqli_getshell',
 '万魔宗通过 SQL 注入直取 Webshell。',
 'SQL 注入 INTO OUTFILE/GetShell。',
 'flag{sqli_getshell_73}', 60, 73, 1, 1),
('QY-LX-04', '【青云宗·炼虚】.htaccess 诡道', 'qiingong', 'lianxu', 5, 'upload_htaccess',
 '上传 .htaccess 自定义解析。',
 'Apache .htaccess 解析漏洞。',
 'flag{upload_htaccess_74}', 60, 74, 1, 1),
('LH-LX-05', '【轮回宗·炼虚】NTFS 流', 'lunhuizong', 'lianxu', 5, 'upload_ntfs',
 '轮回宗在 Windows 下可利用 NTFS 流绕过。',
 'NTFS 备用数据流绕过（教学演示）。',
 'flag{upload_ntfs_stream_75}', 60, 75, 1, 1),
('WM-LX-06', '【万魔宗·炼虚】strcmp 陷阱', 'wanmozong', 'lianxu', 5, 'php_strcmp',
 'strcmp 接收数组返回 NULL，配合 == 绕过。',
 'PHP strcmp 数组绕过。',
 'flag{php_strcmp_array_76}', 60, 76, 1, 1),
('QY-LX-07', '【青云宗·炼虚】mysqli 多语句', 'qiingong', 'lianxu', 5, 'sqli_multi',
 'mysqli_multi_query 允许多语句执行。',
 'mysqli_multi_query 多语句注入。',
 'flag{sqli_multi_query_77}', 60, 77, 1, 1),
('LH-LX-08', '【轮回宗·炼虚】Docker 越界', 'lunhuizong', 'lianxu', 5, 'container_escape',
 '轮回宗尝试从 Docker 容器越界到宿主机。',
 '容器逃逸基础（教学演示）。',
 'flag{container_escape_78}', 60, 78, 1, 1),
('WM-LX-09', '【万魔宗·炼虚】CGI 漏洞', 'wanmozong', 'lianxu', 5, 'php_cgi',
 '万魔宗利用 PHP-CGI 漏洞直接 RCE。',
 'PHP-CGI 漏洞利用。',
 'flag{php_cgi_exploit_79}', 60, 79, 1, 1),
('QY-LX-10', '【青云宗·炼虚】缓存成魔', 'qiingong', 'lianxu', 5, 'cache_poison_adv',
 '青云宗的缓存被恶意污染。',
 '高级缓存投毒。',
 'flag{cache_poison_adv_80}', 60, 80, 1, 1);

-- ===========================
-- 第七阶段：合体期 (L5 剧情) - 10关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('LH-HT-01', '【轮回宗·合体】试炼塔·XSS篇', 'lunhuizong', 'heti', 5, 'xss_comprehensive',
 '试炼塔顶层需要你综合运用 XSS 三大类型（反射/存储/DOM）',
 'XSS 综合：反射+存储+DOM 三子关。',
 'flag{xss_tower_81}', 80, 81, 1, 1),
('WM-HT-02', '【万魔宗·合体】魔窟·反序列化篇', 'wanmozong', 'heti', 5, 'deserialize_comprehensive',
 '魔窟深处有多个反序列化陷阱，需要构造完整 POP 链。',
 '反序列化综合：__wakeup + POP链 + Phar + Session。',
 'flag{deserialize_dungeon_82}', 80, 82, 1, 1),
('QY-HT-03', '【青云宗·合体】藏经阁·SQL篇', 'qiingong', 'heti', 5, 'sqli_comprehensive',
 '藏经阁 SQL 考验：从基础注入到 GetShell 完整链路。',
 'SQL 注入综合：UNION+盲注+GetShell。',
 'flag{sqli_library_83}', 80, 83, 1, 1),
('LH-HT-04', '【轮回宗·合体】轮回殿·认证篇', 'lunhuizong', 'heti', 5, 'auth_comprehensive',
 '轮回殿多重认证缺陷：会话固定+JWT+密码重置。',
 '认证综合：会话固定+JWT+密码重置漏洞。',
 'flag{auth_palace_84}', 80, 84, 1, 1),
('WM-HT-05', '【万魔宗·合体】炼魂殿·SSRF篇', 'wanmozong', 'heti', 5, 'ssrf_comprehensive',
 '炼魂殿有内网探测 + Redis攻击 + 协议利用。',
 'SSRF 综合：内网探测 + Redis + 协议利用。',
 'flag{ssrf_soul_temple_85}', 80, 85, 1, 1),
('QY-HT-06', '【青云宗·合体】阵法台·CSRF篇', 'qiingong', 'heti', 5, 'csrf_comprehensive',
 '阵法台多道阵法需绕过 Token+CORS+Cookie。',
 'CSRF 综合：Token+CORS+SameSite+Referer。',
 'flag{csrf_array_86}', 80, 86, 1, 1),
('LH-HT-07', '【轮回宗·合体】幽冥界·密码学篇', 'lunhuizong', 'heti', 5, 'crypto_comprehensive',
 '幽冥界的密码学谜题：ECB + Hash 扩展 + JWT + Padding Oracle。',
 '密码学综合。',
 'flag{crypto_underworld_87}', 80, 87, 1, 1),
('WM-HT-08', '【万魔宗·合体】血池·命令注入篇', 'wanmozong', 'heti', 5, 'rce_comprehensive',
 '血池 RCE 综合：空格+关键字+无字母数字。',
 '命令注入综合。',
 'flag{rce_blood_pool_88}', 80, 88, 1, 1),
('QY-HT-09', '【青云宗·合体】禁地·代码审计篇', 'qiingong', 'heti', 5, 'code_review',
 '审计一个迷你 CMS，找出全部漏洞。',
 '代码审计综合挑战。',
 'flag{cms_audit_89}', 80, 89, 1, 1),
('LH-HT-10', '【轮回宗·合体】万魔殿·业务逻辑篇', 'lunhuizong', 'heti', 5, 'logic_comprehensive',
 '万魔殿业务逻辑综合：支付+并发+状态机绕过。',
 '业务逻辑综合。',
 'flag{logic_demon_palace_90}', 80, 90, 1, 1);

-- ===========================
-- 第八阶段：大乘期 (L5 终极) - 10关
-- ===========================
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('DC-01', '【大乘】跨宗渗透·青云→万魔', 'wanderer', 'dacheng', 5, 'cross_sect',
 '飞升前的终极考验：从青云宗内部网络渗透到万魔宗禁地。',
 '跨宗门综合渗透（多漏洞链）。',
 'flag{ascend_qy_to_wm_91}', 100, 91, 1, 1),
('DC-02', '【大乘】跨宗渗透·万魔→轮回', 'wanderer', 'dacheng', 5, 'cross_sect',
 '从万魔宗反攻轮回殿。',
 '跨宗门渗透（SSRF+反序列化+XSS）。',
 'flag{ascend_wm_to_lh_92}', 100, 92, 1, 1),
('DC-03', '【大乘】电商系统完整渗透', 'wanderer', 'dacheng', 5, 'web_pentest',
 '完整渗透一个电商系统：用户登录→商品浏览→下单→支付。',
 '电商系统 Web 渗透综合。',
 'flag{ecomm_full_93}', 100, 93, 1, 1),
('DC-04', '【大乘】社交平台逻辑漏洞', 'wanderer', 'dacheng', 5, 'logic_pentest',
 '社交平台的逻辑漏洞综合：粉丝+关注+私信+状态机。',
 '社交平台逻辑漏洞综合。',
 'flag{social_logic_94}', 100, 94, 1, 1),
('DC-05', '【大乘】CMS 代码审计挑战', 'wanderer', 'dacheng', 5, 'cms_audit',
 '审计一个完整的 CMS 系统，找出 5 个以上漏洞。',
 'CMS 综合代码审计。',
 'flag{cms_full_audit_95}', 100, 95, 1, 1),
('DC-06', '【大乘】API 安全全链路', 'wanderer', 'dacheng', 5, 'api_security',
 'REST/GraphQL API 的全链路安全挑战。',
 'API 安全综合。',
 'flag{api_full_chain_96}', 100, 96, 1, 1),
('DC-07', '【大乘】内网穿透完整链', 'wanderer', 'dacheng', 5, 'intranet',
 'Web 漏洞 → 内网 → 域控 → 提权的完整链。',
 '内网渗透完整链（模拟）。',
 'flag{intranet_full_97}', 100, 97, 1, 1),
('DC-08', '【大乘】真实 CVE 复现·ThinkPHP5 RCE', 'wanderer', 'dacheng', 5, 'cve_replay',
 '复现 ThinkPHP 5.0.x 远程代码执行漏洞。',
 '真实 CVE 复现：ThinkPHP 5.0.23 RCE。',
 'flag{cve_thinkphp5_98}', 100, 98, 1, 1),
('DC-09', '【大乘】CTF 夺旗综合题', 'wanderer', 'dacheng', 5, 'ctf_pwn',
 'CTF 风格的综合夺旗挑战。',
 'CTF 综合题（多步骤）。',
 'flag{ctf_pwn_99}', 100, 99, 1, 1),
('DC-10', '【大乘·飞升】终极挑战', 'wanderer', 'dacheng', 5, 'ultimate',
 '修真之巅，飞升在即。需综合运用所学，击破所有护山大阵。',
 '修真靶场终极飞升挑战（多漏洞协同）。',
 'flag{ascend_dacheng_ultimate_100}', 200, 100, 1, 1);
