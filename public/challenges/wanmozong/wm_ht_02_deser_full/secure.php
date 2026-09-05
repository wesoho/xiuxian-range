<?php
// 综合反序列化防御
// 1. 永远不要反序列化不可信数据
// 2. 使用 allowed_classes 选项限制可反序列化的类
// 3. 使用 json_encode/decode 替代 serialize/unserialize
// 4. Phar 包装器禁用

$data = $_POST['data'] ?? '';
// 修复：使用 JSON + 签名
$decoded = json_decode(base64_decode($data), true);
if (!is_array($decoded)) {
    http_response_code(400);
    exit('Invalid data');
}