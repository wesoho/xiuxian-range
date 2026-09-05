<?php
// QY-LQ-05 secure.php - 防御
/**
 * 安全实践：
 * 1. 生产环境禁用所有 phpinfo 文件
 * 2. CI/CD 加入 phpinfo 检测
 * 3. Web 服务器禁止访问 phpinfo.php
 * 4. 调试通过日志或 XDebug 实现，而非公开页面
 */
header('HTTP/1.1 403 Forbidden');
echo 'Disabled in production';