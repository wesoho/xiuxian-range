<?php
/**
 * 后台 - 切换关卡启用状态
 */
\XiuXian\Core\Csrf::verifyOrFail();

$id = $_POST['challenge_id'] ?? '';
$enabled = (int) ($_POST['enabled'] ?? 0);

if (!$id) {
    json_fail('参数错误');
}

\XiuXian\Models\Challenge::setEnabled($id, (bool) $enabled);
logger()->info('Admin toggle challenge', ['id' => $id, 'enabled' => $enabled]);
json_ok(null, '操作成功');