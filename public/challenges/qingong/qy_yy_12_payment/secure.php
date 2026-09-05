<?php
/**
 * QY-YY-12 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：服务端重新计算价格
$prices = ['sword' => 100, 'shield' => 50, 'potion' => 20];
$item = $_POST['item'] ?? '';
$price = $prices[$item] ?? 0;  // 服务端价格，不信任客户端
if (!$price) {
    http_response_code(400);
    exit('Invalid item');
}
echo "已购买 $item，价格 $price";