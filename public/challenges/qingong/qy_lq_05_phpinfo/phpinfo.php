<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// ============================================================
// QY-LQ-05 phpinfo.php - 故意保留的演示文件
// 教学用：演示 phpinfo() 的敏感信息泄露
// ============================================================

// 真实环境中**绝对不应**保留此文件
phpinfo();

// 修真靶场演示用：在页面底部嵌入提示
echo "\n<!--\n修真靶场小提示：phpinfo 会泄露服务器环境、PHP 配置、临时目录、环境变量等敏感信息。\n生产环境绝对不能保留此文件。\n本关 Flag：" . xxr_challenge_flag() . "\n-->\n";

// 彩蛋（QY-LQ-05 附加）：假 phpinfo 区块，藏着一枚寻宝口令
// —— 与 phpinfo 原生输出同款样式，眼尖的人会在这里多看一眼
echo "\n<div class=\"phpinfo\" style=\"margin:24px 0;\">\n";
echo "<h2><a name=\"module_xxr_egg\">Environment</a></h2>\n";
echo "<h2 class=\"text-gold\">XXR_EGG（隐藏变量）</h2>\n";
echo "<table>\n<tr><td class=\"e\">XXR_EGG </td><td class=\"v\">flag{egg_phpinfo_eye} </td></tr>\n";
echo "<tr><td class=\"e\">XXR_EGG_HINT </td><td class=\"v\">这面「配置之镜」照出了不该照出的东西。口令请到 /tianji 天机阁兑换 </td></tr>\n";
echo "</table>\n</div>\n";
