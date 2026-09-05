#!/bin/bash
# 修真靶场 - 容器启动脚本
# 等待数据库就绪后再启动 Apache

set -e

echo ""
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║           ⚔️  修真网络安全靶场 (XiuXian Range)            ║"
echo "║                  v1.0.0  正在启动...                      ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# 1. 等待 MySQL 就绪
echo "🔮 等待 MySQL 觉醒..."
MAX_TRIES=60
TRIES=0
until mysqladmin ping -h "$DB_HOST" -u "root" -p"rootpass" --silent 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "❌ 数据库连接超时（${MAX_TRIES} 次尝试），仍继续启动 Apache（可能部分功能不可用）"
        break
    fi
    if [ $((TRIES % 5)) -eq 0 ]; then
        echo "  ⏳ 第 $TRIES/$MAX_TRIES 次尝试连接数据库..."
    fi
    sleep 2
done

if [ $TRIES -lt $MAX_TRIES ]; then
    echo "✅ 数据库已就绪"
fi

# 2. 等待 Redis 就绪（不影响核心功能，超时直接继续）
echo "🔮 等待 Redis 觉醒..."
TRIES=0
until redis-cli -h "$REDIS_HOST" ping 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge 10 ]; then
        echo "⚠️  Redis 连接超时（不影响核心功能）"
        break
    fi
    sleep 1
done

if [ $TRIES -lt 10 ]; then
    echo "✅ Redis 已就绪"
fi

# 3. 确保存储目录存在且可写
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/cache \
         /var/www/html/storage/sessions \
         /var/www/html/storage/uploads \
         /var/www/html/public/uploads

chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true

# 4. 检查数据库是否已初始化（通过检查 users 表是否存在）
echo "🔮 检查数据库境界..."
TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "root" -p"rootpass" -N -e "USE $DB_DATABASE; SHOW TABLES LIKE 'users';" 2>/dev/null | grep "users" || true)

if [ -z "$TABLE_EXISTS" ]; then
    echo "📜 数据库为空，正在运行建表与种子数据..."

    # 建表
    if [ -f /docker-entrypoint-initdb.d/01_schema.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /docker-entrypoint-initdb.d/01_schema.sql 2>&1 | head -20 || {
            mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/01_schema.sql || true
        }
    elif [ -f /var/www/html/database/init/01_schema.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/01_schema.sql || true
    fi

    # 关卡数据
    if [ -f /docker-entrypoint-initdb.d/02_seed.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /docker-entrypoint-initdb.d/02_seed.sql 2>&1 | head -20 || {
            mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/02_seed.sql || true
        }
    elif [ -f /var/www/html/database/init/02_seed.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/02_seed.sql || true
    fi

    # 提示数据（300 条）
    if [ -f /docker-entrypoint-initdb.d/03_hints.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /docker-entrypoint-initdb.d/03_hints.sql 2>&1 | head -10 || {
            mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/03_hints.sql || true
        }
    elif [ -f /var/www/html/database/init/03_hints.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/03_hints.sql || true
    fi

    # 彩蛋系统数据（彩蛋登记 / 万宝楼 / 斗法台题库 / 宗门秘史）
    if [ -f /docker-entrypoint-initdb.d/04_eggs.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /docker-entrypoint-initdb.d/04_eggs.sql 2>&1 | head -10 || {
            mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/04_eggs.sql || true
        }
    elif [ -f /var/www/html/database/init/04_eggs.sql ]; then
        mysql -h "$DB_HOST" -u "root" -p"rootpass" "$DB_DATABASE" < /var/www/html/database/init/04_eggs.sql || true
    fi

    # 关卡 Flag 随机化（防猜测 / 防仓库泄露；关卡页面经 xxr_challenge_flag() 动态渲染）
    mysql -h "$DB_HOST" -u "root" -p"rootpass" -e \
        "UPDATE \`$DB_DATABASE\`.challenges SET flag = CONCAT('flag{', SUBSTRING(MD5(RAND()), 1, 16), '}');" || true
    echo "✅ 关卡 Flag 已随机化"

    # 彩蛋口令随机化（揭示点经 xxr_egg_secret() 动态渲染），并同步《宗门秘史》暗格口令
    mysql -h "$DB_HOST" -u "root" -p"rootpass" -e \
        "UPDATE \`$DB_DATABASE\`.easter_eggs SET secret = CONCAT('flag{egg_', SUBSTRING(MD5(RAND()), 1, 12), '}') WHERE secret IS NOT NULL;
         UPDATE \`$DB_DATABASE\`.secret_manual SET content = REPLACE(content, 'flag{egg_sect_manual}', (SELECT t.secret FROM (SELECT secret FROM \`$DB_DATABASE\`.easter_eggs WHERE code = 'egg_sect_secret') t));" || true
    echo "✅ 彩蛋口令已随机化"

    echo "✅ 数据库初始化完成（含 100 关元数据 + 300 条提示 + 彩蛋系统）"
else
    echo "✅ 数据库已存在，跳过初始化"
fi

# 5. 创建 .env（如果不存在）
if [ ! -f /var/www/html/.env ]; then
    cat > /var/www/html/.env <<EOF
APP_NAME="修真网络安全靶场"
APP_ENV=${APP_ENV:-development}
APP_DEBUG=${APP_DEBUG:-true}
APP_KEY=xxr-$(date +%s)-$(openssl rand -hex 8 2>/dev/null || echo $RANDOM)

DB_HOST=${DB_HOST}
DB_PORT=3306
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=xiuxian
DB_PASSWORD=xiuxian_pass

REDIS_HOST=${REDIS_HOST}
REDIS_PORT=6379

SESSION_LIFETIME=7200
EOF
    chmod 644 /var/www/html/.env
    echo "✅ 环境配置已生成"
fi

# 6. PHP 依赖检查（Composer）
if [ -f /var/www/html/composer.json ] && [ ! -d /var/www/html/vendor ]; then
    echo "📦 检测到 composer.json，尝试安装依赖..."
    if command -v composer >/dev/null 2>&1; then
        cd /var/www/html && composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5 || echo "⚠️  Composer 安装失败，使用内置自动加载"
    else
        echo "⚠️  未安装 Composer，使用内置自动加载"
    fi
fi

# 7. 健康检查端点可用性
echo "🏥 健康检查..."
curl -fsS http://localhost/healthz >/dev/null 2>&1 || echo "  ⚠️  健康检查端点暂未响应（正常，Apache 启动后即恢复）"

echo ""
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║  ✅  修真靶场启动成功！                                    ║"
echo "║                                                            ║"
echo "║  🌐 访问地址: http://localhost:8080                        ║"
echo "║  🗄  数据库管理: http://localhost:8081                      ║"
echo "║  👤 默认账号: admin / xxr_admin_2026                        ║"
echo "║                                                            ║"
echo "║  🧘 愿道友早日飞升大乘！                                    ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# 8. 执行 CMD（启动 Apache）
exec "$@"