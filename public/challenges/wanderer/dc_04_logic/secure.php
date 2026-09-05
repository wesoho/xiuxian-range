<?php
/**
 * DC-04 secure.php - 社交平台逻辑防御
 */

echo '<h2>社交平台 · 业务逻辑安全</h2>';

echo '<h3>👥 关注关系安全</h3>';
echo '<ul>';
echo '<li>关注/取消关注使用 POST + CSRF Token</li>';
echo '<li>关注关系检查（黑名单、拉黑）</li>';
echo '<li>关注数量限制（防爬虫）</li>';
echo '</ul>';

echo '<h3>💬 私信安全</h3>';
echo '<ul>';
echo '<li>非好友私信限制</li>';
echo '<li>敏感词过滤</li>';
echo '<li>举报机制</li>';
echo '<li>内容审核（人工 + AI）</li>';
echo '</ul>';

echo '<h3>📊 状态机安全</h3>';
echo '<ul>';
echo '<li>订单状态严格流转（待支付→已支付→已发货→已收货→已评价）</li>';
echo '<li>每个状态变更需要权限校验</li>';
echo '<li>状态变更日志</li>';
echo '</ul>';