<?php
// WM-LQ-10 secure.php - 安全实践
/**
 * 修复：
 * 1. 修改默认管理路径（避免 /admin、/manager 等）
 * 2. 强制鉴权 + 双因素认证
 * 3. IP 白名单限制访问
 * 4. WAF 检测扫描行为
 * 5. 修改所有默认账号密码
 */
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'admin') {
    http_response_code(403);
    header('Location: /login');
    exit;
}

// 加载管理后台
require __DIR__ . '/admin/index.php';