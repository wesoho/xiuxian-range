<?php
// XXE 文件读取
$xml = $_POST['xml'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>魔影重重·XXE</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>👹 魔影重重</h2>
        <form method="POST">
            <textarea name="xml" class="form-control" rows="6" placeholder='<!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><foo>&xxe;</foo>'></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($xml) {
            // 【漏洞】未禁用外部实体
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            echo htmlspecialchars($dom->saveXML());
        }
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xxe'); ?>
</body>
</html>