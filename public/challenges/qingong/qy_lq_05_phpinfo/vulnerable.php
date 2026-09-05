<?php
// QY-LQ-05 vulnerable.php - phpinfo() 信息泄露
/**
 * 漏洞分析：
 * phpinfo() 会输出 PHP 全部配置：环境变量、临时目录、扩展、请求头、服务器信息等。
 * 真实案例：MySQL 密码曾因 phpinfo 泄露。
 */
phpinfo();