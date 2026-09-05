<?php
// LH-LQ-07 vulnerable.php - 仅前端校验
/**
 * 漏洞：依赖 JS 校验输入。
 * 攻击者可禁用 JS 或直接改包绕过。
 *
 * 真实案例：常见于 CTF 入门题、电商试用价绕过等。
 */
?>
<script>
function validateForm() {
    // 仅前端校验
    if (document.getElementById('username').value.length < 3) return false;
    return true;
}
</script>