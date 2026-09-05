# 添加新关卡指南

本指南说明如何向修真靶场添加一个新的关卡。

---

## 🎯 步骤一览

1. 创建关卡目录
2. 编写 `index.php`（入口）
3. 编写 `vulnerable.php`（漏洞代码）
4. 编写 `secure.php`（安全代码）
5. 在数据库添加关卡元数据
6. 添加三级提示
7. 测试关卡
8. 提交 PR

---

## 1️⃣ 创建关卡目录

关卡编号格式：`[宗门代码][境界代码]-[序号]`
- 宗门代码：`QY`（青云）、`WM`（万魔）、`LH`（轮回）、`ZS`（综合）
- 境界代码：`LQ`（炼气）、`JZ`（筑基）、`JD`（金丹）、`YY`（元婴）、`HS`（化神）、`LX`（炼虚）、`HT`（合体）、`DC`（大乘）

例如：第 11 关（青云宗·筑基期）编号为 `QY-JZ-01`

目录命名规则：将编号转换为小写下划线格式：

```
public/challenges/qingong/qy_jz_01/
```

---

## 2️⃣ 编写 index.php（入口）

修真叙事 + 关卡操作界面。模板：

```php
<?php
/**
 * ============================================================
 * 关卡 ID · 关卡标题
 * 修真叙事：一段引人入胜的修真故事
 * 漏洞类型：XXE / SQL注入 / ...
 * 难度：L1-L5
 * ============================================================
 */
session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>关卡标题 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">关卡标题</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 一段修真叙事……
        </div>

        <!-- 关卡操作区 -->

        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>
```

---

## 3️⃣ 编写 vulnerable.php（漏洞代码）

**故意留有漏洞**的代码，用于演示给学员。注意：

- ✅ 漏洞要典型、有教学价值
- ✅ 修真靶场默认 `display_errors=On`，可演示错误回显
- ✅ 修真靶场默认 `allow_url_include=On`，可演示 RFI
- ❌ 不要使用真实生产环境的弱配置（除非教学需要）
- ❌ 不要引入真实漏洞的 exploit（仅演示漏洞本身）

示例：数字型 SQL 注入

```php
<?php
// vulnerable.php - 数字型 SQL 注入
$id = $_GET['id'] ?? '1';

try {
    $pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
    
    // 【漏洞】直接拼接 SQL
    $stmt = $pdo->query("SELECT username FROM demo_users WHERE id = $id");
    
    foreach ($stmt as $row) {
        echo "<p>弟子：" . htmlspecialchars($row['username']) . "</p>";
    }
} catch (PDOException $e) {
    // 【漏洞】显示错误信息
    echo "错误：" . $e->getMessage();
}
```

---

## 4️⃣ 编写 secure.php（安全代码）

**修复后**的安全代码，作为对照：

```php
<?php
// secure.php - 参数化查询
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('Invalid ID');
}

$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);

foreach ($stmt as $row) {
    echo "<p>弟子：" . htmlspecialchars($row['username']) . "</p>';
}
```

---

## 5️⃣ 添加数据库元数据

在 `database/seeds/02_challenges.sql` 末尾追加：

```sql
INSERT INTO `challenges` (`id`, `title`, `sect`, `realm`, `difficulty`, `category`, `narrative`, `description`, `flag`, `points`, `order_num`, `source_viewable`, `enabled`) VALUES
('QY-JZ-01', '【青云宗·筑基】关卡标题', 'qiingong', 'zhuji', 2, 'sqli_numeric',
 '修真叙事（100-200字）……',
 '关卡简介（50-100字）',
 'flag{your_flag_here}', 15, 11, 1, 1);
```

> ⚠️ **Flag 随机化**：`flag` 列的值在数据库初始化时会被整体随机替换（16 位随机 hex），
> 此处填写的只是初始占位值。关卡页面若需要向玩家展示 Flag，**必须**在页面顶部引入
> `<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; ?>`
> 并用 `<?= xxr_challenge_flag() ?>` 动态渲染，**禁止把 Flag 字符串硬编码进页面**。

字段说明：
- `id`：唯一编号
- `title`：带修真叙事的标题
- `sect`：宗门（qiingong/wanmozong/lunhuizong/wanderer）
- `realm`：境界（liqi/zhuji/jindan/yuanying/huashen/lianxu/heti/dacheng）
- `difficulty`：1-5
- `category`：漏洞分类（sql/xss/csrf/...）
- `narrative`：剧情（HTML 转义后展示）
- `description`：简短描述
- `flag`：通关标志（初始化时自动随机化，此处仅为占位）
- `points`：奖励点数
- `order_num`：展示顺序（同一境界内递增）
- `source_viewable`：是否允许查看源码（建议 1）
- `enabled`：是否启用（0/1）

---

## 6️⃣ 添加三级提示

在 `database/init/02_seed.sql` 或单独的 hints 文件中：

```sql
INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES
('QY-JZ-01', 1, '弱提示（不揭示方法）', 0, 1),
('QY-JZ-01', 2, '中等提示（给技术线索）', 5, 2),
('QY-JZ-01', 3, '完整答案（具体 Payload）', 15, 3);
```

提示设计原则：
- **L1 弱提示**：仅指明方向（如"该漏洞与用户输入有关"）
- **L2 中等提示**：给出技术线索（如"检查 SQL 语句拼接"）
- **L3 完整答案**：完整 Payload 或步骤

---

## 7️⃣ 测试关卡

```bash
# 重新导入种子数据
docker exec -i xxr-db mysql -u root -prootpass xiuxian_range \
  < database/init/02_seed.sql

# 访问测试
open http://localhost:8080/challenge/QY-JZ-01

# 测试 Flag 提交
# 在关卡页提交 flag{your_flag_here}
```

测试清单：
- [ ] 关卡可正常打开
- [ ] 漏洞可被正确利用
- [ ] Flag 提交后正确通过
- [ ] 通关后境界点数正确累加
- [ ] 源码对比功能正常
- [ ] 提示系统正常
- [ ] 移动端可正常浏览

---

## 8️⃣ 提交 PR

```bash
git checkout -b feat/add-qy-jz-01
git add public/challenges/qingong/qy_jz_01/
git add database/seeds/02_challenges.sql
git commit -m "feat: add QY-JZ-01 数字型SQL注入关卡"
git push origin feat/add-qy-jz-01
```

PR 模板：

```markdown
## 新增关卡：QY-JZ-01 数字型SQL注入

### 修真叙事
（简要描述剧情）

### 漏洞类型
SQL 注入（数字型）

### 难度
L2 初级

### 教学目标
- 理解 SQL 注入原理
- 掌握数字型注入手法
- 学会使用参数化查询防御

### 测试
- [x] 关卡可打开
- [x] Flag 可被提交
- [x] 源码对比正常
```

---

## 📚 命名规范参考

### 关卡分类（category 字段）

| 分类 | 说明 |
|------|------|
| `info_leak` | 信息泄露 |
| `weak_password` | 弱口令 |
| `client_validate` | 客户端校验 |
| `misconfig` | 默认配置 |
| `sqli_*` | SQL 注入（按子类型） |
| `xss_*` | XSS（按子类型） |
| `csrf_*` | CSRF（按子类型） |
| `rce_*` | 命令注入（按子类型） |
| `upload_*` | 文件上传（按子类型） |
| `lfi_*` | 文件包含（按子类型） |
| `xxe_*` | XXE（按子类型） |
| `ssrf_*` | SSRF（按子类型） |
| `deserialize_*` | 反序列化（按子类型） |
| `idor_*` | 越权（按子类型） |
| `jwt_*` | JWT（按子类型） |
| `crypto_*` | 密码学（按子类型） |
| `php_*` | PHP 特性漏洞 |
| `payment_*` | 支付漏洞 |
| `captcha_*` | 验证码漏洞 |
| `brute_force` | 暴力破解 |
| `*_comprehensive` | 综合剧情关卡 |

### 修真叙事模板

```
[道友身份]在[地点]遇到[挑战]，
[背景描述]，
[如何观察]，
[提示：应从什么方向思考]。
```

示例：
> 你在青云宗藏经阁整理典籍时，发现网页源码的 HTML 注释中似乎藏着什么……
> 掌门远游前留下的寄语？
> 在浏览器中按 Ctrl+U 查看页面源代码，留意注释标记。

---

## 🎓 教学设计建议

### 关卡难度梯度

- **L1 入门**：单步漏洞，零基础可解
- **L2 初级**：基础漏洞，需简单绕过
- **L3 中级**：过滤绕过，需一定技巧
- **L4 高级**：组合利用，需创造性
- **L5 专家**：综合挑战，接近真实场景

### 修真叙事贴合度

- **青云宗**（正道）：XSS/CSRF/认证/代码审计
- **万魔宗**（魔道）：反序列化/RCE/SSRF/逻辑漏洞
- **轮回宗**（中立）：SQL注入/XXE/密码学/上传

---

## ❓ 常见问题

### Q: 关卡文件组织？
A: 每个关卡独立目录，命名必须与数据库 `id` 对应（小写下划线）。

### Q: 如何测试而不影响其他用户？
A: 可以在本地创建副本关卡（id 不同），通关验证后再合并。

### Q: Flag 必须唯一吗？
A: 是的。每个 Flag 全平台唯一，建议带关卡分类前缀。

### Q: 数据库连接如何处理？
A: 修真靶场 Docker 部署时用 `db` 作为主机名，本地部署用 `localhost`。

---

🧘 感谢您为修真靶场贡献关卡！愿道友早登大乘！