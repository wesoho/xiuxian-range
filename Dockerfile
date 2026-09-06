# 修真网络安全靶场 - 主应用镜像
# 基于官方 PHP 8.2 Apache 镜像，内置常用扩展以演示各类漏洞
FROM php:8.2-apache

LABEL maintainer="李叔AI <admin@xiuxian-range.local>" \
      version="1.0.0" \
      description="修真网络安全靶场 - PHP Web Security Practice Range"

# 1. 安装系统依赖与常用工具
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        wget \
        unzip \
        zip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        default-mysql-client \
        nano \
        iputils-ping \
        net-tools \
    && rm -rf /var/lib/apt/lists/*

# 2. 安装 PHP 扩展（覆盖靶场常见需求）
# pdo_mysql   - 数据库连接
# mysqli      - 演示 mysql_* 与 mysqli_* 注入差异
# gd          - 图片马、二次渲染
# zip         - 演示 zip 协议、备份文件
# xml         - XXE 演示
# mbstring    - 宽字节注入
# fileinfo    - getimagesize
# curl        - SSRF 演示
# redis       - Redis 反序列化（可选）
# opcache     - 性能
# pcntl/posix - 部分高级漏洞需要
RUN docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        xml \
        mbstring \
        fileinfo \
        curl \
        bcmath \
        opcache \
    && pecl install redis-6.0.2 \
    && docker-php-ext-enable redis

# 3. Apache 配置
RUN a2enmod rewrite headers ssl \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && sed -i 's/#ServerName www.example.com/ServerName localhost/g' /etc/apache2/sites-available/000-default.conf

# 4. 拷贝应用代码
WORKDIR /var/www/html

# 先复制 composer 配置（利用 Docker 缓存）
COPY composer.json* /var/www/html/

# 安装 Composer（如果存在 composer.json）
RUN if [ -f composer.json ]; then \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
        && composer install --no-dev --optimize-autoloader --no-interaction || true; \
    fi

# 复制源代码
COPY . /var/www/html/

# 5. 设置权限（确保运行时用户可写日志、上传、Session）
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/storage/logs \
                /var/www/html/storage/cache \
                /var/www/html/storage/sessions \
                /var/www/html/storage/uploads \
                /var/www/html/public/uploads \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/database

# 6. 复制 Apache 站点配置
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/xx-custom.ini

# 7. 暴露端口
EXPOSE 80

# 8. 健康检查
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD curl -fsS http://localhost/healthz || exit 1

# 9. 启动脚本（数据库等待 + 启动 Apache）
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]