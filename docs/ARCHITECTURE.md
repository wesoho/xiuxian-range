# 架构说明

## 🎯 设计目标

1. **教学透明**：原生 PHP 代码，每行可审计
2. **工程优雅**：分层架构、PSR-4、关注点分离
3. **可扩展**：插件机制、API 接口、模块化关卡
4. **安全**：平台自身代码遵循安全最佳实践

---

## 📐 整体架构

```
┌─────────────────────────────────────────────────────┐
│                    浏览器 (弟子)                       │
└────────────────────┬────────────────────────────────┘
                     │ HTTP/HTTPS
┌────────────────────▼────────────────────────────────┐
│              Apache 2.4 (mod_rewrite)                │
│                    :80/:443                          │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│            public/index.php (入口)                    │
│  ┌──────────────────────────────────────────────┐   │
│  │  Router (路由分发)                             │   │
│  │  ├── HomeController (主页)                     │   │
│  │  ├── AuthController (认证)                     │   │
│  │  ├── ChallengeController (关卡)               │   │
│  │  └── LeaderboardController (排行榜)            │   │
│  └──────────────────────────────────────────────┘   │
│                       │                              │
│  ┌────────────────────▼──────────────────────────┐   │
│  │  Service Layer (业务服务)                      │   │
│  │  ├── LevelService (境界晋升)                  │   │
│  │  ├── ChallengeService (关卡业务)              │   │
│  │  └── ...                                       │   │
│  └────────────────────┬──────────────────────────┘   │
│                       │                              │
│  ┌────────────────────▼──────────────────────────┐   │
│  │  Model Layer (数据模型)                        │   │
│  │  ├── User / Challenge / Progress               │   │
│  │  └── ...                                       │   │
│  └────────────────────┬──────────────────────────┘   │
└───────────────────────┼──────────────────────────────┘
                        │ PDO (prepared statements)
        ┌───────────────┴───────────────┐
        ▼                               ▼
   ┌─────────┐                    ┌─────────┐
   │ MySQL 8 │                    │  Redis 7│
   └─────────┘                    └─────────┘
```

---

## 📦 分层职责

### 1. 入口层（public/index.php）
- 加载 Composer / 自动加载
- 启动 Session
- 注册全局异常处理
- 定义路由
- 分发请求

### 2. 控制器层（app/Controllers/）
- 接收 HTTP 请求
- 调用 Service 完成业务逻辑
- 渲染视图返回响应
- **不做**业务逻辑处理

### 3. 服务层（app/Services/）
- 业务规则与流程编排
- 事务管理
- 调用 Model 完成数据操作
- 可被多个 Controller 复用

### 4. 模型层（app/Models/）
- 封装数据访问（PDO）
- 提供 CRUD 方法
- 不包含业务逻辑

### 5. 核心框架（app/Core/）
- Database（PDO 封装）
- Session（会话管理）
- Csrf（CSRF Token）
- Router（路由分发）
- Auth（认证）
- Logger（日志）
- View（视图渲染）

### 6. 辅助函数（app/Helpers/）
- 全局函数（env, e, url, redirect, csrf_field 等）
- 安全辅助（safe_sql, validate_csrf, rate_limit 等）
- HTTP 响应（json_response, view, abort 等）

---

## 🗃 数据库设计

### 核心表

| 表名 | 作用 |
|------|------|
| `users` | 修真弟子账号 |
| `challenges` | 100 关卡元数据 |
| `progress` | 弟子闯关记录 |
| `hints` | 关卡三级提示 |
| `badges` | 修真徽章定义 |
| `user_badges` | 弟子获得的徽章 |
| `writeups` | 弟子公开的 Writeup |
| `challenge_logs` | 关卡行为审计 |
| `demo_users` | 关卡内部演示用账号 |
| `settings` | 系统配置 |

### 索引策略
- `users.username`, `users.email` 唯一索引
- `progress(user_id, challenge_id)` 复合唯一索引
- `challenges.realm`, `challenges.sect`, `challenges.category` 索引
- `challenge_logs.created_at` 时间索引

---

## 🔄 请求生命周期

```
1. 浏览器发送 GET /challenge/QY-LQ-01
        ↓
2. Apache mod_rewrite 路由到 public/index.php
        ↓
3. 入口加载自动加载器、辅助函数
        ↓
4. 启动 Session、设置时区
        ↓
5. Router 匹配路由 → ChallengeController::show($id)
        ↓
6. Controller 调用 ChallengeService::detail()
        ↓
7. Service 调用 Challenge::find() + Progress::get()
        ↓
8. Model 通过 PDO prepared statement 查询
        ↓
9. 返回数据 → Controller 渲染 View
        ↓
10. View 包含 layout → 输出 HTML
        ↓
11. 浏览器渲染页面
```

---

## 🔐 安全机制

### 1. 平台自身的安全实践

| 风险点 | 防御措施 |
|--------|----------|
| SQL 注入 | 所有平台代码使用 PDO prepared statement |
| XSS | 所有输出使用 `htmlspecialchars()` |
| CSRF | 全局 CSRF Token 验证 |
| 会话固定 | 登录后重新生成 Session ID |
| 暴力破解 | Session 限流（rate_limit） |
| 密码泄露 | Argon2id 哈希 |
| 点击劫持 | X-Frame-Options 头 |
| MIME 嗅探 | X-Content-Type-Options 头 |

### 2. 平台与关卡的隔离

- **平台代码**位于 `app/`、`public/index.php`、`admin/`，**必须**遵守安全实践
- **关卡代码**位于 `public/challenges/<宗门>/<关卡>/`，**故意**有漏洞用于教学
- 数据库连接使用同一个 MySQL，但平台表与关卡表分离
- 关卡目录单独权限，可通过 `.htaccess` 控制

### 3. CSRF Token 实现

```php
// 生成
$token = bin2hex(random_bytes(32));
$_SESSION['_csrf_token'] = $token;

// 验证
hash_equals($_SESSION['_csrf_token'], $_POST['_token']);
```

### 4. 会话管理

- HttpOnly Cookie（防 XSS 窃取）
- SameSite=Lax（防 CSRF）
- 定期重新生成 Session ID
- gc_maxlifetime 控制过期

---

## 🧩 关卡架构

### 关卡目录结构

```
public/challenges/
├── qingong/                          # 青云宗目录
│   ├── qy_lq_01_html_comment/        # 关卡 1
│   │   ├── index.php                 # 入口（修真叙事）
│   │   ├── vulnerable.php            # 漏洞代码（教学用）
│   │   ├── secure.php                # 安全代码（参考）
│   │   └── ... 其他辅助文件
│   └── ...
├── wanmozong/                        # 万魔宗
└── lunhuizong/                       # 轮回宗
```

### 关卡元数据（数据库）

存储在 `challenges` 表：
- `id`（如 QY-LQ-01）
- `title`、`narrative`、`description`（修真叙事）
- `category`（漏洞分类）
- `flag`（通关标志）
- `points`（修真点数）

### 关卡生命周期

```
locked → unlocked → in_progress → completed
   │         │            │           │
   │         │            │           └─ 通关，奖励点数
   │         │            └─ 试炼中（打开关卡、提交Flag）
   │         └─ 满足解锁条件（前一关完成）
   └─ 默认状态
```

---

## 🚀 部署架构

### 单机部署

```
┌──────────────────────────────────┐
│  Docker Host                     │
│  ┌────────┐  ┌────┐  ┌────────┐ │
│  │ web    │  │ db │  │ redis  │ │
│  │ :8080  │  │    │  │        │ │
│  └────┬───┘  └─┬──┘  └────┬───┘ │
│       └────────┴──────────┘     │
│         xxr-net (bridge)         │
└──────────────────────────────────┘
```

### 生产级部署（推荐）

```
Internet → Cloudflare → Nginx(SSL) → Apache(PHP-FPM)
                                          ↓
                              ┌───────────┼───────────┐
                              ↓           ↓           ↓
                          MySQL主从   Redis集群   文件存储
```

---

## 🔌 扩展点

### 1. 添加新关卡
- 创建 `public/challenges/<sect>/<id>/index.php`
- 在 `database/seeds/02_challenges.sql` 添加元数据
- 添加 `hints`、`badges` 数据

### 2. 添加新宗门
- 修改数据库 `users.sect` 枚举
- 修改 `LevelService` / `Challenge` 模型
- 修改视图中的宗门徽章 CSS

### 3. 接入 CTF 平台
- 通过 REST API（`/api/...`）获取关卡数据
- 提交 Flag 通过 `/challenge/submit-flag` 接口
- 用户身份通过 JWT 或 Session Token 同步

### 4. 多语言支持
- 提取视图中的中文文案到 `app/Lang/zh-CN.php`
- 添加 `app/Lang/en-US.php`
- 实现 `__()` 函数或使用 Twig 模板引擎

---

## 📊 性能指标

- 单机 Docker：支持 100 并发用户
- 数据库查询：平均 < 5ms（含索引）
- 页面渲染：平均 < 100ms
- Session 存储：文件（约 1KB / 用户）

如需更高性能：
- 启用 OPcache
- 使用 Redis 缓存 Session
- 静态资源 CDN
- MySQL 主从读写分离