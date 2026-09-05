<?php
// QY-LQ-04 secure.php - 安全实践
/**
 * 修复：
 * 1. 备份文件存储在 webroot 之外
 * 2. 部署流水线排除备份文件
 * 3. Apache: <FilesMatch "\.(zip|bak|tar|sql)$"> Require all denied </FilesMatch>
 * 4. 定期扫描泄露风险
 */
?>