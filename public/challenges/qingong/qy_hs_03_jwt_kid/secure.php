<?php
/**
 * QY-HS-03 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：kid 不允许包含路径分隔符
$kid = preg_replace('/[^a-zA-Z0-9_-]/', '', $header['kid'] ?? '');