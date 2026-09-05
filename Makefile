# 修真网络安全靶场 - Makefile
# 修真之巅，飞升在即。
#
# 常用命令：
#   make help       - 显示帮助
#   make up         - 启动靶场
#   make down       - 停止靶场
#   make restart    - 重启靶场
#   make reset      - 重置数据库（清空所有数据）
#   make logs       - 查看日志
#   make test       - 运行单元测试
#   make lint       - PHP 语法检查
#   make db-shell   - 进入 MySQL 控制台
#   make shell      - 进入 Web 容器

.PHONY: help up down restart reset logs test lint db-shell shell

help:
	@echo "╔════════════════════════════════════════════════════╗"
	@echo "║     ⚔️  修真网络安全靶场 - Makefile 命令           ║"
	@echo "╠════════════════════════════════════════════════════╣"
	@echo "║  make up         启动靶场                          ║"
	@echo "║  make down       停止靶场                          ║"
	@echo "║  make restart    重启靶场                          ║"
	@echo "║  make reset      重置数据库（⚠️  清空所有数据）    ║"
	@echo "║  make logs       查看 Web 日志                     ║"
	@echo "║  make test       运行 PHPUnit 单元测试             ║"
	@echo "║  make lint       PHP 语法检查                      ║"
	@echo "║  make db-shell   进入 MySQL 控制台                 ║"
	@echo "║  make shell      进入 Web 容器                     ║"
	@echo "╚════════════════════════════════════════════════════╝"

up:
	docker-compose up -d
	@echo "✅ 修真靶场已启动"
	@echo "🌐 访问 http://localhost:8080"
	@echo "🗄  数据库管理 http://localhost:8081"

down:
	docker-compose down

restart:
	docker-compose restart

reset:
	@echo "⚠️  即将清空所有数据..."
	@read -p "确认？[y/N] " r && [ "$$r" = "y" ]
	docker-compose down -v
	docker-compose up -d

logs:
	docker-compose logs -f web

db-shell:
	docker exec -it xxr-db mysql -u xiuxian -pxiuxian_pass xiuxian_range

shell:
	docker exec -it xxr-web bash

test:
	@if [ ! -d vendor ]; then \
		echo "📦 安装 Composer 依赖..."; \
		composer install --no-interaction || true; \
	fi
	@if [ ! -x vendor/bin/phpunit ]; then \
		echo "📦 安装 PHPUnit..."; \
		composer require --dev phpunit/phpunit --no-interaction || true; \
	fi
	vendor/bin/phpunit --testdox --colors=always

lint:
	@echo "=== 检查 PHP 语法 ==="
	@FAIL=0; \
	for f in $$(find app public -name '*.php'); do \
		result=$$(php -l "$$f" 2>&1); \
		if [[ "$$result" != *"No syntax errors"* ]]; then \
			echo "❌ $$f"; \
			echo "$$result"; \
			FAIL=1; \
		fi; \
	done; \
	if [ $$FAIL -eq 0 ]; then \
		echo "✅ 所有 PHP 文件语法正确"; \
	else \
		exit 1; \
	fi

# 完整重建（清理 + 重装）
clean:
	docker-compose down -v
	docker system prune -f
	docker-compose up -d --build

# 备份数据库
backup:
	@mkdir -p backups
	docker exec xxr-db mysqldump -u xiuxian -pxiuxian_pass xiuxian_range | gzip > backups/xxr-$$(date +%Y%m%d-%H%M%S).sql.gz
	@echo "✅ 数据库已备份到 backups/"

# 恢复数据库
restore:
	@ls -lt backups/*.sql.gz 2>/dev/null | head -1
	@echo "使用方法: gunzip -c backups/<file>.sql.gz | docker exec -i xxr-db mysql -u xiuxian -pxiuxian_pass xiuxian_range"