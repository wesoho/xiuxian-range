<?php
/**
 * WM-HS-07 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：使用 HTTP/2、严格 CL/TE 解析、统一的代理配置
// Apache: mod_proxy 配置严格 CLF 格式
// Nginx: proxy_http_version 1.1 + 严格 header 解析