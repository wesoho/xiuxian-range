<?php
// 修复：禁用外部实体
libxml_disable_entity_loader(true);  // PHP < 8.0
$xml = $_POST['xml'] ?? '';

$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);  // PHP 8.0+
echo htmlspecialchars($dom->saveXML());