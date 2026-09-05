# ⚔️ 修真网络安全靶场 (XiuXian Range)

> 以中国修真文化为载体的 PHP Web 安全攻防教学平台
> 从炼气到大乘，一路修真一路飞升。

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

---

## 🎯 项目简介

**修真网络安全靶场（XiuXian Range）** 是一个**面向个人和团队**的网络安全教学平台，
以中国**修真文化**为载体，将传统网络安全漏洞学习包装成"修真炼道"之旅。

**核心特色**：

- 🥉🥈🥇💎 **修真八阶境界体系**：炼气 → 筑基 → 金丹 → 元婴 → 化神 → 炼虚 → 合体 → 大乘
- 🏯🔥🔮 **三大宗门世界观**：青云宗（正道）、万魔宗（魔道）、轮回宗（中立）
- 📖⚔️🌟 **三阶段学习路径**：习道（学） → 试炼（战） → 悟道（复盘）
- 🎯 **100 关卡覆盖**：OWASP Top 10 + 业务逻辑 + 现代漏洞
- 💎 **源码对比视图**：左侧漏洞代码 / 右侧安全代码
- 🏆 **排行榜 / 徽章系统**：积分、宗门排名、成就解锁
- 🥚 **彩蛋系统**：Konami 秘籍、五环寻宝链、每日求签/答题/悬赏、灵兽图鉴
- ⚡ **飞升大典**：100 关全通触发九重天雷渡劫动画 + 通关文牒 + 谢幕卷轴

---

## 🚀 一键启动（推荐）

只需一条命令，整个修真靶场即可运行：

```bash
# 1. 克隆项目
git clone https://github.com/yourname/xiuxian-range.git
cd xiuxian-range

# 2. 启动（Docker 会自动构建镜像 + 初始化数据库）
docker-compose up -d

# 3. 等待 30 秒后访问
# 🎮 修真靶场:        http://localhost:8080
# 🗄  数据库管理 (Adminer): http://localhost:8081
```

**默认账号**：

| 角色 | 用户名 | 密码 | 说明 |
|------|--------|------|------|
| 长老（管理员） | `admin` | `xxr_admin_2026` | 后台管理权限 |
| 弟子 | `qingyun` | `xxr123456` | 青云宗 |
| 弟子 | `wanmo` | `xxr123456` | 万魔宗 |
| 弟子 | `lunhui` | `xxr123456` | 轮回宗 |

> ⚠️ 上线生产环境前请立即修改默认密码！

---

## 📂 项目结构

```
xiuxian-range/
├── docker-compose.yml        # 一键启动配置
├── Dockerfile                # Web 应用镜像
├── docker/                   # Docker 相关配置
├── public/                   # Web 根目录
│   ├── index.php             # 应用入口
│   ├── challenges/           # 关卡目录（含漏洞代码）
│   │   ├── qingong/          # 青云宗
│   │   ├── wanmozong/        # 万魔宗
│   │   └── lunhuizong/       # 轮回宗
│   └── assets/               # 静态资源
├── app/                      # 应用核心
│   ├── Core/                 # 框架核心（路由/数据库/会话/CSRF）
│   ├── Models/               # 数据模型
│   ├── Services/             # 业务服务
│   ├── Controllers/          # 控制器
│   ├── Helpers/              # 辅助函数
│   └── Views/                # 视图模板
├── challenges/               # 关卡业务代码（独立存放）
├── database/                 # 数据库
│   ├── migrations/           # 迁移脚本
│   ├── seeds/                # 种子数据（100关卡）
│   └── init/                 # Docker 初始化脚本
├── admin/                    # 后台管理
├── storage/                  # 运行时存储（日志/缓存/Session）
├── tests/                    # 单元测试
└── docs/                     # 项目文档
```

---

## 🎓 修真境界地图

| 境界 | 关卡 | 主题 |
|------|------|------|
| 🥉 **炼气期** | 10 关 | 信息泄露、弱口令、HTTP 头等基础 |
| 🥉 **筑基期** | 15 关 | XSS、CSRF、SQL 注入基础、命令注入 |
| 🥈 **金丹期** | 15 关 | 过滤绕过、盲注、文件包含、上传 |
| 🥈 **元婴期** | 15 关 | XXE、SSRF、反序列化、越权、支付 |
| 🥇 **化神期** | 15 关 | JWT、OAuth、CORS、密码学、PHP 弱类型 |
| 🥇 **炼虚期** | 10 关 | 综合 RCE、SQLi GetShell、解析漏洞 |
| 💎 **合体期** | 10 关 | 剧情综合挑战 |
| 💎 **大乘期** | 10 关 | 终极跨宗门渗透、真实 CVE 复现 |

**总计：100 关，覆盖 OWASP Top 10 + 业务逻辑 + 现代 Web 漏洞。**

---

## 🥚 趣味玩法与彩蛋

除了闯关，靶场里还藏着一套完整的趣味系统（**只发徽章装扮，不发积分**，不影响排行榜公平）：

| 玩法 | 入口 | 内容 |
|------|------|------|
| 🔮 天机阁 | `/tianji` | 每日求签、彩蛋口令兑换、彩蛋收集册、天机残页收集 |
| ⚔️ 斗法台 | `/doufatai` | 每日 10 题安全知识，答对 8 题领灵石，连胜排行 |
| 📜 悬赏令 | `/xuanshang` | 每日 3 条随机悬赏任务 |
| 🏮 万宝楼 | `/wanbaolou` | 灵石兑换头衔装扮（御 SQL 者 / 撕码狂魔……） |
| 🌌 秘境 | `/mijing` | 五张天机残页集齐后开启 |

**隐藏彩蛋**（不剧透，只提示）：Konami 秘籍、连点导航印章、控制台留言、robots.txt、
404 藏头诗、藏经阁的深层注释、丹房数据库的暗格、过期符文的夹层……

**飞升大典**：100 关全通触发九重天雷渡劫动画，授予金光昵称与专属主题，
生成可打印的「通关文牒」，并解锁谢幕卷轴（彩蛋答案全披露）。

> 维护者文档（含答案披露）：[docs/easter-eggs.md](docs/easter-eggs.md)

---

## 📚 三阶段学习路径

每个关卡都遵循「习道 → 试炼 → 悟道」的三阶段流程：

### 第一阶段：习道 📖
- 阅读剧情背景（修真叙事）
- 了解漏洞原理
- 学习攻击思路

### 第二阶段：试炼 ⚔️
- 进入真实靶场环境
- 三级提示系统（弱/中/完整）
- 提交 Flag 通关

### 第三阶段：悟道 🌟
- 查看完整 Writeup
- 源码对比（漏洞 vs 安全）
- 根因分析与防御方案

---

## 🛠️ 技术栈

| 层级 | 选型 |
|------|------|
| 后端语言 | PHP 8.2（原生，无框架） |
| Web 服务器 | Apache 2.4 (mod_rewrite) |
| 数据库 | MySQL 8.0 + Redis 7 |
| 前端 | Bootstrap 5 + 自研修真风格主题 |
| 包管理 | Composer（可选） |
| 容器化 | Docker + Docker Compose |
| 字符编码 | utf8mb4 |

---

## ⚙️ 常用命令

```bash
# 启动靶场
docker-compose up -d

# 查看日志
docker-compose logs -f web

# 进入容器调试
docker exec -it xxr-web bash

# 重置数据库（清空所有数据）
docker-compose down -v
docker-compose up -d

# 停止靶场
docker-compose down
```

---

## 🧪 本地无 Docker 启动

```bash
# 1. 安装 PHP 8.2 + MySQL 8.0 + Apache
# 推荐使用 XAMPP / PHPStudy / MAMP

# 2. 创建数据库
mysql -u root -p
> CREATE DATABASE xiuxian_range CHARACTER SET utf8mb4;
> exit
mysql -u root -p xiuxian_range < database/init/01_schema.sql
mysql -u root -p xiuxian_range < database/init/02_seed.sql

# 3. 配置数据库连接（创建 .env 文件）
cp .env.example .env
# 编辑 .env 填入你的数据库配置

# 4. 启动 Apache + 浏览
# 浏览器访问 http://localhost
```

---

## 🔐 安全说明

### ⚠️ 仅供教学使用
- 本靶场包含**故意设置的漏洞代码**
- **绝对不要**将本项目部署到公网生产环境
- **绝对不要**将漏洞代码用于任何非法用途

### 🛡️ 平台自身的安全实践
- 平台代码使用参数化查询（PDO prepared statement）
- 密码使用 Argon2id 哈希
- CSRF Token 全局保护
- Session ID 定期重新生成
- HTTP 安全头（X-Frame-Options 等）

### 📚 学习建议
- 每个关卡都有「源码对比视图」展示漏洞代码与安全代码
- 通关后必看「悟道」面板的根因分析
- 用 Burp Suite 等工具辅助练习
- 在隔离环境（虚拟机/Docker）中运行

---

## 🤝 贡献

欢迎贡献新的关卡、修复 Bug、改进文档！

### 添加新关卡流程

1. 在 `public/challenges/<宗门目录>/<关卡目录>/` 下创建目录
2. 编写 `index.php`、`vulnerable.php`、`secure.php`
3. 在 `database/seeds/02_challenges.sql` 添加元数据
4. 在 `database/seeds/03_hints.sql` 添加提示
5. 提交 PR

---

## 📜 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件

---

## 🙏 致谢

本项目参考了以下优秀靶场：
- [DVWA](https://github.com/digininja/DVWA)
- [sqli-labs](https://github.com/Audi-1/sqli-labs)
- [upload-labs](https://github.com/c0ny1/upload-labs)
- [Pikachu](https://github.com/zhuifengshaonianhanlu/pikachu)
- [OWASP WebGoat](https://github.com/WebGoat/WebGoat)
- [OWASP Juice Shop](https://github.com/juice-shop/juice-shop)

---

<div align="center">

⚔️ **愿道友早日飞升大乘！** ⚔️

</div>