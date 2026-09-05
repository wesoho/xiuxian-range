<?php
// 修复：
// 1. Docker 容器以非 root 用户运行
// 2. 禁用 --privileged
// 3. 启用 seccomp / AppArmor
// 4. 最小权限原则（只读文件系统、drop capabilities）
// 5. 使用 Distroless 镜像