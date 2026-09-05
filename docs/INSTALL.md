# 安装指南

修真网络安全靶场支持两种部署方式：
- **Docker 一键部署**（推荐）
- **本地手动部署**

---

## 🐳 方式一：Docker 一键部署（推荐）

### 系统要求
- Docker Engine 20.10+
- Docker Compose v2.0+
- 至少 2GB 可用内存
- 至少 5GB 可用磁盘

### 步骤

```bash
# 1. 克隆项目
git clone https://github.com/yourname/xiuxian-range.git
cd xiuxian-range

# 2. 启动（首次会自动下载镜像，可能需要 5-10 分钟）
docker-compose up -d

# 3. 查看启动状态
docker-compose ps

# 4. 查看 Web 服务日志
docker-compose logs -f web

# 5. 浏览器访问
# 修真靶场:     http://localhost:8080
# 数据库管理:   http://localhost:8081
```

### 验证安装

```bash
# 健康检查
curl http://localhost:8080/healthz
# 应返回: OK

# 查看数据库是否初始化
docker exec -it xxr-db mysql -u root -prootpass xiuxian_range -e "SELECT COUNT(*) FROM challenges;"
# 应返回: 100
```

### 数据持久化

数据库数据保存在 Docker 卷 `xxr_db_data` 中，重启容器不会丢失。

完全重置（**会删除所有数据**）：
```bash
docker-compose down -v
docker-compose up -d
```

---

## 💻 方式二：本地手动部署

### 系统要求
- PHP 8.2+ （需要扩展：pdo_mysql, mysqli, gd, mbstring, xml, curl）
- MySQL 8.0+ 或 MariaDB 10.6+
- Apache 2.4+ (启用 mod_rewrite)
- Redis 7+（可选）

### 步骤

#### 1. 安装 PHP 与扩展

**Ubuntu/Debian**:
```bash
sudo apt update
sudo apt install -y php8.2 php8.2-mysql php8.2-gd php8.2-mbstring \
                    php8.2-xml php8.2-curl php8.2-zip php8.2-redis
```

**macOS (Homebrew)**:
```bash
brew install php@8.2
brew install mysql redis
```

**Windows**: 推荐使用 [XAMPP](https://www.apachefriends.org/) 或 [PHPStudy](https://www.xp.cn/)

#### 2. 创建数据库

```bash
mysql -u root -p
> CREATE DATABASE xiuxian_range CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> CREATE USER 'xiuxian'@'localhost' IDENTIFIED BY 'xiuxian_pass';
> GRANT ALL PRIVILEGES ON xiuxian_range.* TO 'xiuxian'@'localhost';
> FLUSH PRIVILEGES;
> exit;
```

#### 3. 初始化数据库

```bash
cd xiuxian-range
mysql -u root -p xiuxian_range < database/init/01_schema.sql
mysql -u root -p xiuxian_range < database/init/02_seed.sql
```

#### 4. 配置环境

```bash
cp .env.example .env
# 编辑 .env 文件，修改数据库连接信息
# DB_HOST=localhost
# DB_USERNAME=xiuxian
# DB_PASSWORD=xiuxian_pass
```

#### 5. 启动 Apache

```bash
# Ubuntu
sudo systemctl start apache2

# macOS
brew services start httpd

# XAMPP/PHPStudy
# 通过控制面板启动
```

#### 6. 部署项目

将 `xiuxian-range/` 整个目录复制到 Apache 的 DocumentRoot：
- **Ubuntu/Debian**: `/var/www/html/`
- **macOS Homebrew**: `/usr/local/var/www/`
- **XAMPP**: `C:\xampp\htdocs\`
- **PHPStudy**: 选择的网站根目录

浏览器访问 `http://localhost/` 或 `http://localhost/xiuxian-range/public/`

---

## 🚀 方式三：本机快速启动（SQLite，无需 Docker/MySQL）

适合本机快速体验/演示（如 Windows + PHP CLI）。仅需 PHP 8.2+（扩展：pdo_sqlite, mbstring）。

```bash
# 1. 初始化 SQLite 开发库（storage/xxr_dev.db）
php tools/init_sqlite_dev.php

# 2. 配置 .env
#    DB_CONNECTION=sqlite
#    DB_DATABASE=H:/bachang/storage/xxr_dev.db   （SQLite 文件绝对路径）
#    APP_URL=http://127.0.0.1:8686

# 3. 启动（内置服务器，server.php 为路由脚本，docroot 必须是 public/）
php -S 127.0.0.1:8080 -t public server.php
```

> 端口可自选；若端口被占用（Windows 常见），换一个即可，如 `-S 127.0.0.1:8686`。

**默认账号**：`admin / xxr_admin_2026`（长老）；测试弟子 `qingyun / wanmo / lunhui`，密码 `xxr123456`。

**注意**：
- SQLite 模式与生产 MySQL 模式共用同一套种子数据（`database/init/`），SQL 均已做跨库兼容（引号转义用 `''`，时间用 `CURRENT_TIMESTAMP`）。
- 提交 Flag、提示购买、境界晋升、排行榜等核心流程在两种模式下均可运行；关卡目录静态页由内置服务器直接返回。

## ❓ 常见问题

### Q: 旧版本升级后需要跑哪些迁移？
A: 按版本顺序对既有库执行（全新部署由初始化脚本自动完成，无需手动）：
- `006_easter_eggs.sql` —— 彩蛋系统 / 趣味玩法相关表 + `users.ascended_at`
- `007_randomize_flags.sql` —— 关卡 Flag 随机化（防猜测/防仓库泄露）
- `008_randomize_egg_secrets.sql` —— 彩蛋口令随机化（同步《宗门秘史》暗格）
- `009_quiz_score_detail.sql` —— 斗法台逐题解析列

MySQL：`mysql -u root -p xiuxian_range < database/migrations/00X_xxx.sql`
本地 SQLite：直接重跑 `php tools/init_sqlite_dev.php`（会重置进度并重新随机化）。

### Q: 启动后访问 500 错误？
A: 检查 Apache `AllowOverride All` 是否开启，确认 `mod_rewrite` 已启用。

### Q: 数据库连接失败？
A: 检查 `.env` 中的 `DB_HOST` 是否正确。Docker 部署用 `db`，本地部署用 `localhost`。

### Q: 提交 Flag 显示 "Token 不正确"？
A: 浏览器禁用了 Cookie 或 PHP Session 配置有问题。检查 `storage/sessions` 目录可写。

### Q: 关卡页面 404？
A: 确认 `public/challenges/<宗门>/<关卡>/index.php` 文件存在。

### Q: 内置服务器下 CSS/JS 全部 404？
A: 必须带 `-t public` 启动：`php -S 127.0.0.1:8686 -t public server.php`（docroot 必须是 public，否则静态资源与关卡页面都无法命中）。

### Q: 如何重置整个环境？
```bash
docker-compose down -v
docker-compose up -d
```

---

## 🔧 高级配置

### 修改默认密码

登录管理员账号后，在修真档案页面修改密码，或直接修改数据库：

```sql
UPDATE users SET password_hash = '$argon2id$...' WHERE username = 'admin';
```

使用 PHP 生成新哈希：
```php
echo password_hash('your-new-password', PASSWORD_ARGON2ID);
```

### 启用 HTTPS

参考 `docker/000-default.conf` 添加 SSL 配置：

```apache
<VirtualHost *:443>
    ServerName your-domain.com
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    DocumentRoot /var/www/html/public
</VirtualHost>
```

### 性能调优

修改 `docker/php.ini` 中的：
- `memory_limit = 256M`
- `opcache.enable = 1`
- `max_execution_time = 60`

数据库连接池：在 `app/Core/Database.php` 添加长连接支持。