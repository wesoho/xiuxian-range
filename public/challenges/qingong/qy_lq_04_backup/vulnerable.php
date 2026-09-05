<?php
// QY-LQ-04 vulnerable.php - 备份文件泄露
/**
 * 漏洞分析：
 * 备份文件（如 www.zip）若放在 webroot 目录，可被任意下载。
 * 攻击者可还原整个站点源码、获取 .env、配置文件等。
 */
?>