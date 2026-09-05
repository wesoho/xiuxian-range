<?php
// 修复：使用 textContent 而非 innerHTML
?>
<div id="output"></div>
<script>
const hash = location.hash.substring(1);
document.getElementById('output').textContent = hash;  // 安全
</script>