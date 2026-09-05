<?php
// 修复：缓存键包含用户身份
header('Vary: Cookie, Authorization, User-Agent, X-Forwarded-Host');
header("Cache-Control: private, no-cache");