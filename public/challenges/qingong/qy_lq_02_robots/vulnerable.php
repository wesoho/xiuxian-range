<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// ============================================================
// QY-LQ-02 vulnerable.php - 漏洞演示
// 漏洞：在 robots.txt 中泄露了敏感信息路径与提示
// ============================================================

/**
 * 漏洞分析：
 *
 * 开发者使用 robots.txt 阻止搜索引擎爬取 /admin/ 路径，
 * 反而暴露了敏感路径的存在。
 * 真实案例：Shopify、GitLab 都曾因 robots.txt 泄露过敏感信息。
 */

// robots.txt 内容（模拟静态文件）
$robots = <<<TXT
User-agent: *
Disallow: /admin/
Disallow: /private/
TXT;

// 输出到响应
header('Content-Type: text/plain');
echo $robots;
echo "\n# 开发者备注：\n";
echo "# 藏经阁入口：/private/  \n";
echo "# Flag: " . xxr_challenge_flag() . "\n";