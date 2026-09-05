@echo off
rem 修真靶场 - 本地开发服务器一键启动
rem 必须带 -t public 指定站点根目录，否则静态资源（CSS/JS/图片）会 404
cd /d %~dp0
echo 🏯 修真靶场 · http://127.0.0.1:8686  （Ctrl+C 停止）
php -S 127.0.0.1:8686 -t public server.php
