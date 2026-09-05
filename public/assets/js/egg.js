/* 修真网络安全靶场 - 彩蛋系统脚本（egg.js）
 *
 * 由 footer.php 全局引入，承载四类彩蛋：
 *  1. Konami 秘籍（↑↑↓↓←→←→BA）  -> 上报 /egg/konami + 灵气特效
 *  2. 连点 brand 九下            -> 上报 /egg/whistle + 点亮「✨天机」导航
 *  3. 控制台喊话                 -> 每个会话一次，藏寻宝链第一条线索
 *  4. 页脚灵兽（发呆 3 分钟路过） -> 点击抓捕上报 /egg/crane
 *  5. 接管通关弹窗：data.ascended 时追加「渡劫飞升」入口
 *
 * 设计约束：不依赖 xxr.js 的存在（404 页等也有彩蛋），全部自带降级。
 */
(function () {
    'use strict';

    var EGG_NAV_KEY = 'xxr_tianji_nav';
    var QI_KEY = 'xxr_qi_until';

    // ---------- 基础工具 ----------

    function toast(message, type) {
        if (window.xxr && window.xxr.toast) {
            window.xxr.toast(message, type || 'warning');
            return;
        }
        var div = document.createElement('div');
        div.style.cssText = 'position:fixed;top:80px;right:20px;z-index:99999;padding:14px 24px;color:#fff;' +
            'background:linear-gradient(135deg,#d4af37,#b8941f);border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.4);' +
            'max-width:340px;font-size:14px;';
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(function () { div.remove(); }, 5000);
    }

    function post(url, data) {
        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        // 关卡页 / 404 页等没有 CSRF token 的页面不发请求，
        // 彩蛋的本地效果（灵气特效、导航点亮）照常生效
        if (!token) {
            return Promise.resolve({ code: 1, message: '' });
        }
        var fd = new FormData();
        fd.append('_token', token);
        Object.keys(data || {}).forEach(function (k) { fd.append(k, data[k]); });
        return fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .catch(function () { return { code: 1, message: '网络异常' }; });
    }

    // ---------- 1. Konami 秘籍 ----------

    var KONAMI = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
    var konamiPos = 0;

    document.addEventListener('keydown', function (ev) {
        var key = ev.key.length === 1 ? ev.key.toLowerCase() : ev.key;
        konamiPos = (key === KONAMI[konamiPos]) ? konamiPos + 1 : (key === KONAMI[0] ? 1 : 0);
        if (konamiPos === KONAMI.length) {
            konamiPos = 0;
            triggerKonami();
        }
    });

    function triggerKonami() {
        qiEffect(10 * 60);
        post('/egg/konami', {}).then(function (res) {
            if (res && res.code === 0) {
                toast(res.message, 'success');
            } else if (res && res.message) {
                toast(res.message, 'info');
            }
        });
    }

    // ---------- 灵气特效（金色符箓飘落 10 分钟） ----------

    var GLYPHS = ['符', '灵', '气', '道', '丹', '剑', '罡', '诀'];

    function qiEffect(seconds) {
        if (document.getElementById('xxrQiLayer')) return;
        var until = Date.now() + seconds * 1000;
        try { localStorage.setItem(QI_KEY, String(until)); } catch (e) { /* 隐私模式忽略 */ }

        var layer = document.createElement('div');
        layer.id = 'xxrQiLayer';
        for (var i = 0; i < 24; i++) {
            var s = document.createElement('span');
            s.className = 'xxr-qi-glyph';
            s.textContent = GLYPHS[i % GLYPHS.length];
            s.style.left = (Math.random() * 100) + 'vw';
            s.style.animationDelay = (Math.random() * 12) + 's';
            s.style.animationDuration = (9 + Math.random() * 8) + 's';
            s.style.fontSize = (14 + Math.random() * 18) + 'px';
            layer.appendChild(s);
        }
        document.body.appendChild(layer);
        setTimeout(function () { layer.remove(); }, seconds * 1000);
    }

    // 恢复未过期的灵气特效
    (function restoreQi() {
        var until = 0;
        try { until = parseInt(localStorage.getItem(QI_KEY) || '0', 10); } catch (e) { return; }
        var remain = Math.floor((until - Date.now()) / 1000);
        if (remain > 5) qiEffect(remain);
        else { try { localStorage.removeItem(QI_KEY); } catch (e) { /* noop */ } }
    })();

    // ---------- 2. 连点品牌九下：敲门 -> 天机阁 ----------

    var brandClicks = 0, brandTimer = null;

    document.addEventListener('click', function (ev) {
        var brand = ev.target.closest('.xxr-brand');
        if (!brand) return;
        clearTimeout(brandTimer);
        brandTimer = setTimeout(function () { brandClicks = 0; }, 4000);
        brandClicks++;
        if (brandClicks === 3) toast('印章微微发烫……再敲几下试试？', 'info');
        if (brandClicks >= 9) {
            brandClicks = 0;
            revealTianjiNav(true);
            post('/egg/whistle', {}).then(function (res) {
                if (res && res.code === 0 && res.message) toast(res.message, 'success');
            });
        }
    });

    function revealTianjiNav(persist) {
        var li = document.getElementById('xxrTianjiNav');
        if (li) li.classList.remove('d-none');
        if (persist) {
            try { localStorage.setItem(EGG_NAV_KEY, '1'); } catch (e) { /* noop */ }
        }
    }

    // 已敲过门的浏览器直接点亮
    (function restoreNav() {
        var opened = false;
        try { opened = localStorage.getItem(EGG_NAV_KEY) === '1'; } catch (e) { /* noop */ }
        if (opened) revealTianjiNav(false);
    })();

    // ---------- 3. 控制台喊话 ----------

    (function consoleGreeting() {
        try {
            if (sessionStorage.getItem('xxr_console_seen')) return;
            sessionStorage.setItem('xxr_console_seen', '1');
        } catch (e) { /* noop */ }
        var style = 'color:#d4af37;font-size:16px;font-weight:bold;text-shadow:1px 1px 0 #000;';
        console.log('%c⚔️ 修真网络安全靶场 ⚔️', style);
        console.log('%c此身修行，尽在指尖。别看控制台了，快去闯关。', 'color:#8fa8c8');
        console.log('%c🧾 「天机残页·壹」的线索：连掌门招徕爬虫都要先立规矩——去读读他写下的规矩。', 'color:#d4af37');
        console.log('%c（集齐五张残页，秘境自开。口令请到 /tianji 天机阁兑换）', 'color:#666');
    })();

    // ---------- 4. 页脚灵兽 ----------

    var CRANES = ['🦢', '🦌', '🐢', '🐉', '🐇'];
    var craneSpawned = false;

    function spawnCrane() {
        if (craneSpawned || !document.body) return;
        craneSpawned = true;

        var crane = document.createElement('div');
        crane.className = 'xxr-crane';
        crane.textContent = CRANES[Math.floor(Math.random() * CRANES.length)];
        crane.title = '一只灵兽路过……快点它！';
        crane.addEventListener('click', function () {
            crane.classList.add('xxr-crane-caught');
            post('/egg/crane', {}).then(function (res) {
                toast(res && res.code === 0 ? res.message : '灵兽受惊逃走了（登录后才能抓捕）。', res && res.code === 0 ? 'success' : 'info');
                crane.remove();
            });
        });
        document.body.appendChild(crane);
        // 25 秒没抓到就飞走
        setTimeout(function () { crane.remove(); }, 25000);
    }

    // 发呆检测：180s 内无鼠标/键盘/滚动则灵兽路过
    (function idleWatch() {
        var timer = setTimeout(spawnCrane, 180 * 1000);
        ['mousemove', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (evName) {
            document.addEventListener(evName, function () {
                if (!craneSpawned) {
                    clearTimeout(timer);
                    timer = setTimeout(spawnCrane, 180 * 1000);
                }
            }, { passive: true });
        });
    })();

    // ---------- 5. 接管通关弹窗：飞升入口 ----------

    document.addEventListener('DOMContentLoaded', function () {
        // 金光主题：飞升者全站生效
        if (document.getElementById('xxrThemeFlag')) {
            document.body.classList.add('xxr-theme-gold');
        }

        if (!window.xxr || !window.xxr.showVictory) return;
        var origVictory = window.xxr.showVictory;
        window.xxr.showVictory = function (data) {
            origVictory(data);
            if (data && data.ascended) {
                var grid = document.querySelector('#xxrVictory .d-grid');
                if (grid) {
                    var btn = document.createElement('a');
                    btn.href = '/ascend';
                    btn.className = 'xxr-btn xxr-btn-primary w-100 mb-2 xxr-btn-tribulation';
                    btn.textContent = '⚡ 天劫已至 · 面壁渡劫飞升 ⚡';
                    grid.insertBefore(btn, grid.firstChild);
                }
            }
        };
    });
})();
