<?php
// QY-LQ-03 secure.php - 安全实践
/**
 * 修复方案：
 * 1. 部署前删除 .git、.svn、.hg 等目录
 * 2. CI/CD 中加入 .git 存在性检测
 * 3. Web 服务器配置禁止访问点号开头的目录
 *    Apache: <DirectoryMatch "^\.|\.">Require all denied</DirectoryMatch>
 *    Nginx: location ~ /\. { deny all; }
 * 4. 部署脚本自动清理
 */
?>