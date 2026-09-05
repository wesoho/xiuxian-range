<?php
/**
 * QY-LQ-05 丹房密报 - phpinfo() 信息泄露
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>丹房密报 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">💊 丹房密报</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 丹房某位师兄遗留了一份详细的服务器配置单，意外暴露在了公开路径。
        </div>
        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 直接访问 <a href="/challenges/qingong/qy_lq_05_phpinfo/phpinfo.php" class="text-gold">/phpinfo.php</a>，使用 Ctrl+F 搜索 "flag"。
        </div>
        <h4>📚 phpinfo 泄露的危害</h4>
        <ul>
            <li>暴露 PHP 版本及编译选项</li>
            <li>泄露环境变量（如数据库密码）</li>
            <li>暴露服务器文件系统路径</li>
            <li>暴露已加载扩展、临时目录</li>
        </ul>
        <div class="text-center mt-4">
            <a href="/challenge/QY-LQ-05" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>