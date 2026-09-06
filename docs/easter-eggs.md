# 🥚 彩蛋系统全攻略（Easter Eggs）

> 本文档包含**全部彩蛋答案**，仅供维护者 / 长老查阅。玩家请自行探索！
>
> 玩家可见的说明见 README「趣味玩法」一节。

---

## 一、设计原则

1. **彩蛋只发徽章/称号/装扮，不发闯关积分**（求签/斗法/悬赏的小额灵石除外，且上限极小），保证排行榜公平。
2. 彩蛋口令（`flag{egg_……}`）与正式关卡 flag 完全分离，存于 `easter_eggs.secret`，不影响计分。
3. 徽章记录在首次授予时由 `EggService` 自动建档（`badges` + `user_badges`），无需手工插入。
4. **关卡 Flag 与彩蛋口令均在数据库初始化时随机生成**（见 `tools/init_sqlite_dev.php` /
   `docker/entrypoint.sh` / `migrations/007`、`008`）：
   - 关卡 Flag：16 位随机 hex，页面经 `xxr_challenge_flag()` 动态渲染，**禁止硬编码**；
   - 彩蛋口令：`flag{egg_` + 12 位随机 hex + `}`，全部揭示点经 `xxr_egg_secret()` 动态渲染，
     **禁止硬编码**——不存在可猜的口令，寻宝链必须按线索逐环走。
5. **彩蛋是支线，主任务不受干扰**（手动测试反馈迭代）：
   - 所有彩蛋口令的展示位置必须标注「支线 · 与通关无关」；
   - 与关卡答案同处一页时（如 /robots.txt），主任务答案分区置顶并标注【本关答案】；
   - 寻宝链的下一环线索写**具体操作**（如"在网址后加 ?tianji=1"），不写谜语；
   - 彩蛋口令误投关卡 Flag 框时，明确提示"这是支线彩蛋，本关 Flag 是另一个"。

## 二、彩蛋清单（答案披露）

### 行为触发型（无需口令）

| 彩蛋 | 名称 | 触发方式 |
|------|------|----------|
| `egg_konami` | 🎮 禁术·百晓生 | 全站任意页面键盘输入 ↑↑↓↓←→←→BA |
| `egg_crane` | 🦢 灵兽饲养员 | 任意页面发呆 3 分钟，点击页脚路过的灵兽 |
| `egg_anniversary` | 🎂 守岁修士 | 注册满整年且当日登录（月-日匹配） |
| `egg_tianji_master` | 🌟 天机子 | 集齐五张天机残页后自动授予 + 金光主题 |
| `egg_daozi` | ⚡ 道祖亲临 | **100 关全部通关**（submitFlag 时判定），触发飞升大典 |

### 口令兑换型（到 /tianji 天机阁兑换）

> 所有口令在数据库初始化时随机生成（`flag{egg_` + 12 位 hex），下表为藏匿位置，
> 具体口令值以各揭示点运行时显示为准。

| 彩蛋 | 藏匿位置 |
|------|----------|
| 📖 翻书虫 | QY-LQ-01 藏经阁页面源码**最底部**的第二条注释 |
| 📜 宗门秘史 | QY-JZ-03 SQLi 关卡：`id=-1 UNION SELECT title,content FROM secret_manual`（暗格内容初始化时同步随机口令） |
| 👁 寻宝之眼 | QY-LQ-05 phpinfo 页面底部的仿 phpinfo「XXR_EGG」区块 |
| 🔏 符文解者 | QY-HS-03 关卡页「符文样例」JWT 的中段 payload（base64 解码） |
| 🧾 天机残页·壹 | `/robots.txt` 注释（注意：该文件同时承载 QY-LQ-02 的关卡答案，彩蛋注释在关卡答案下方——修改时两者都要保留） |
| 🧾 天机残页·贰 | 山门 `/?dao=1` 隐藏区块 |
| 🧾 天机残页·叁 | 境界地图 `/?tianji=1` 隐藏区块 |
| 🧾 天机残页·肆 | 404 页面「迷路诗」**源码注释**（藏头=秘境入口 → `/mijing`） |
| 🧾 天机残页·伍 | `/mijing` 秘境页面石门 |

### 寻宝链路线图

```
robots.txt ──hint──> /?dao=1 ──hint──> /challenges?…&tianji=1
    ──hint──> 404 藏头诗「秘境入口」 ──> /mijing ──> 五页集齐 →【天机子】
```

另有两处「线索型」彩蛋不单独发奖：
- **连点导航栏印章 9 次** → 点亮隐藏导航「✨天机」（session 标记，游客亦可）
- **控制台喊话** → 每会话一次，附残页壹的暗示

## 三、飞升大典（100 关全通）

触发点：`ChallengeService::submitFlag` 通关后调用 `checkAscension()`：

1. `users.ascended_at` 写入时间戳（天劫只渡一次）
2. 授予彩蛋 `egg_daozi`（徽章【道祖亲临】）
3. 授予装扮 `theme_gold`（全站金光主题，footer 挂钩 + `egg.js` 应用）
4. API 返回 `ascended: true`，前端通关弹窗追加「⚡ 渡劫飞升」入口
5. `/ascend` 渡劫动画（九道天雷）+ 可打印「通关文牒」
6. `/dacheng-final` 谢幕卷轴 + 彩蛋答案全披露
7. 排行榜：飞升者金光昵称 + 七日内全服喜报横幅

## 四、趣味玩法（每日循环）

| 页面 | 玩法 | 奖励 |
|------|------|------|
| `/tianji` 天机阁 | 每日求签（24 种签文）、口令兑换、彩蛋收集册、灵兽图鉴 | 0–10 灵石 |
| `/doufatai` 斗法台 | 每日 10 题安全知识（全场同题），答对 8 题及格 | +10 灵石、连胜计数 |
| `/xuanshang` 悬赏令 | 每日 3 条随机悬赏（通关/探索/兑换彩蛋等） | 5–20 灵石 |
| `/wanbaolou` 万宝楼 | 灵石购买头衔装扮（御 SQL 者 / 撕码狂魔…），装备后上排行榜 | 纯装扮 |

## 五、技术说明

### 数据表（migrations/006 + init/01_schema + init_sqlite_dev.php 三处同步）

`easter_eggs` / `user_easter_eggs` / `user_slips` / `fortune_draws` / `cosmetics` /
`user_cosmetics` / `quiz_questions` / `quiz_attempts` / `user_bounties` / `user_counters`，
以及 `users.ascended_at` 列与 SQLi 彩蛋表 `secret_manual`。

种子数据：`database/init/04_eggs.sql`（Docker entrypoint 与 `tools/init_sqlite_dev.php` 均已接入）。

### 关键文件

| 文件 | 职责 |
|------|------|
| `app/Services/EggService.php` | 彩蛋授予 / 口令兑换 / 残页 / 徽章建档 / 计数器 |
| `app/Services/FunService.php` | 求签 / 斗法 / 悬赏 / 坊市业务逻辑 |
| `app/Controllers/FunController.php` | 全部趣味玩法路由 |
| `public/assets/js/egg.js` | Konami / 点 logo / 控制台喊话 / 灵兽 / 飞升弹窗接管 |
| `public/assets/css/egg.css` | 灵气特效 / 渡劫动画 / 金光主题 / 文牒打印样式 |
| `app/Views/fun/*.php` | 天机阁 / 万宝楼 / 斗法台 / 悬赏令 / 秘境 / 飞升 / 谢幕视图 |
| `app/bootstrap_challenge.php` | 关卡页复用平台数据库连接（MySQL/SQLite 双兼容） |

### 端点一览

```
GET  /tianji            POST /tianji/draw          POST /egg/claim
GET  /wanbaolou         POST /wanbaolou/buy        POST /wanbaolou/equip
GET  /doufatai          POST /doufatai/submit
GET  /xuanshang         POST /xuanshang/claim
POST /egg/konami        POST /egg/crane            POST /egg/whistle
GET  /mijing            GET  /ascend               GET  /dacheng-final
```

### 兼容性注意

- 所有 SQL 均为 MySQL / SQLite 通用写法（日期比较用 PHP 生成的 `'Y-m-d 00:00:00'` 字符串）。
- 旧库未安装彩蛋表时，navbar / footer 已做 try/catch 静默降级，不影响主功能。
- `egg.js` 不依赖 `xiuxian.js`（404 页等独立页面同样生效），`defer` 加载于页脚。

### 新增一个口令型彩蛋

1. `database/init/04_eggs.sql` 的 `easter_eggs` 中加一行（`secret` 唯一）。
2. 在藏匿点露出该口令。
3. 重建库（或手工 INSERT），无需改代码——收集册自动展示。
