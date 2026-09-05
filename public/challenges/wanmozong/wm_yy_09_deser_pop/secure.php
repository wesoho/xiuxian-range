<?php
// 修复：allowed_classes 限制
$data = $_POST['data'] ?? '';
// 限制可反序列化的类
$obj = @unserialize($data, ['allowed_classes' => []]);  // 完全禁止对象
// 或使用 JSON