/* 修真网络安全靶场 - 通用前端脚本 */

(function () {
    'use strict';

    // 1. CSRF Token 注入到所有 AJAX 请求
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) {
        const origFetch = window.fetch;
        window.fetch = function (url, opts) {
            opts = opts || {};
            opts.headers = opts.headers || {};
            if (opts.method && opts.method.toUpperCase() !== 'GET') {
                opts.headers['X-CSRF-Token'] = csrfToken;
            }
            return origFetch(url, opts);
        };
    }

    // 2. 表单提交时自动附带 CSRF Token
    document.querySelectorAll('form').forEach(form => {
        if (form.method && form.method.toUpperCase() !== 'GET' && !form.querySelector('input[name="_token"]')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken || '';
            form.appendChild(input);
        }
    });

    // 3. 提示按钮自动注入 CSRF
    window.xxr = {
        csrfToken: csrfToken,

        /**
         * 调用 API（POST）
         * 遇 419（CSRF Token 过期/会话更换）自动取新令牌并重试一次
         */
        api: function (url, data, retried) {
            const fd = new FormData();
            fd.append('_token', this.csrfToken);
            for (const k in data) {
                fd.append(k, data[k]);
            }
            return fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json().then(res => ({ res, status: r.status })))
                .then(({ res, status }) => {
                    if (status === 419 && !retried) {
                        return fetch('/csrf-token', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(j => {
                                if (j && j.data && j.data.token) {
                                    this.csrfToken = j.data.token;
                                    const meta = document.querySelector('meta[name="csrf-token"]');
                                    if (meta) meta.setAttribute('content', j.data.token);
                                    return this.api(url, data, true);
                                }
                                return { code: 1, message: '页面已过期，请刷新页面后重试' };
                            });
                    }
                    return res;
                })
                .catch(() => ({ code: 1, message: '网络异常，请稍后再试' }));
        },

        /**
         * Toast 提示
         */
        toast: function (message, type) {
            type = type || 'info';
            const colors = {
                success: 'linear-gradient(135deg, #00a86b, #007a4d)',
                error:   'linear-gradient(135deg, #c0392b, #8b1f13)',
                warning: 'linear-gradient(135deg, #d4af37, #b8941f)',
                info:    'linear-gradient(135deg, #2e86de, #1e5fa3)',
            };
            const div = document.createElement('div');
            div.style.cssText = `
                position: fixed; top: 80px; right: 20px; z-index: 9999;
                padding: 14px 24px; color: white;
                background: ${colors[type]}; border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                animation: xxrSlideIn 0.3s ease-out;
            `;
            div.textContent = message;
            document.body.appendChild(div);
            setTimeout(() => {
                div.style.opacity = '0';
                div.style.transition = 'opacity 0.3s';
                setTimeout(() => div.remove(), 300);
            }, 3500);
        },

        /**
         * 提交 Flag
         */
        submitFlag: function (challengeId, flagValue) {
            this.api('/challenge/submit-flag', {
                challenge_id: challengeId,
                flag: flagValue
            }).then(res => {
                if (res.code === 0) {
                    this.toast(res.message, 'success');
                    this.showVictory(res.data || {});
                } else if (res.code === 2 && res.data && res.data.egg) {
                    // 彩蛋口令投进了关卡框：自动收录并提示去天机阁
                    this.toast(res.message + '（天机阁 → /tianji）', 'info');
                } else {
                    this.toast(res.message, 'error');
                }
            });
        },

        /**
         * 通关弹窗：引导继续闯关
         */
        showVictory: function (data) {
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
            const next = data.next;
            const nextBtn = next
                ? `<a href="/challenge/${esc(next.id)}?phase=learn" class="xxr-btn xxr-btn-primary w-100 mb-2">⚔️ 继续闯关 · ${esc(next.title)}</a>`
                : `<a href="/leaderboard" class="xxr-btn xxr-btn-primary w-100 mb-2">🏆 全境打通 · 查看修真榜</a>`;
            document.body.insertAdjacentHTML('beforeend', `
                <div class="modal fade" id="xxrVictory" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content xxr-victory">
                            <span class="xxr-seal xxr-seal-md">通关</span>
                            <h3>道友，试炼达成！</h3>
                            <p class="xxr-victory-msg">${esc(data.message || '')}</p>
                            <div class="d-grid mt-3">
                                ${nextBtn}
                                <a href="javascript:location.reload()" class="xxr-btn xxr-btn-secondary w-100 mb-2">↻ 再战本关</a>
                                <a href="/challenges" class="xxr-btn xxr-btn-secondary w-100">🗺 回到境界地图</a>
                            </div>
                        </div>
                    </div>
                </div>`);
            const modal = new bootstrap.Modal(document.getElementById('xxrVictory'));
            modal.show();
            document.getElementById('xxrVictory').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        },

        /**
         * 解锁提示
         */
        revealHint: function (challengeId, hintId, level) {
            this.api('/challenge/get-hint', {
                challenge_id: challengeId,
                hint_id: hintId
            }).then(res => {
                if (res.code === 0) {
                    const el = document.querySelector(`#hint-${hintId} .xxr-hint-content`);
                    if (el) {
                        el.innerHTML = '<strong>提示：</strong>' + res.data.content;
                    }
                    this.toast('提示已解锁', 'success');
                } else {
                    this.toast(res.message, 'error');
                }
            });
        }
    };

    // 动画样式
    const style = document.createElement('style');
    style.textContent = `
        @keyframes xxrSlideIn {
            from { transform: translateX(20px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
})();