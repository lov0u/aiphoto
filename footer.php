</main><!-- #main -->

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-footer.png' ); ?>" alt="Aiphoto" class="footer-logo-img">
                </div>
                <p class="footer-tagline">由 Agnes AI 驱动的免费图片创作平台，让您通过文字轻松生成无版权的高清素材</p>
            </div>
            <div class="footer-cols">
                <div class="footer-col">
                    <h4>创作工具</h4>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/generate' ) ); ?>">文生图</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/generate' ) ); ?>">图生图</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>">灵感库</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>关于我们</h4>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/sitemap' ) ); ?>">网站地图</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/disclaimer' ) ); ?>">免责协议</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/disclaimer' ) ); ?>">使用条款</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( bloginfo( 'name' ) ); ?>.
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener">鲁ICP备：2024080965号</a>
                <span class="footer-divider">·</span>
                技术支持：<a href="https://ra0.cn" target="_blank" rel="noopener">青衣网络</a>
            </p>
            <div class="footer-links">
                <a href="<?php echo esc_url( home_url( '/sitemap' ) ); ?>">网站地图</a>
                <span class="footer-divider">·</span>
                <a href="<?php echo esc_url( home_url( '/disclaimer' ) ); ?>">免责协议</a>
                <span class="footer-divider">·</span>
                <span>AI 生成内容仅供参考，请自行审核《如需删除图片请联系管理员：青衣网络》</span>
            </div>
        </div>
    </div>
</footer>

<!-- ===== 悬浮 AI 助手 ===== -->
<style>
/* 悬浮AI助手 - 按钮 */
.ai-float-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(124, 58, 237, 0.35);
    z-index: 9998;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ai-float-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 28px rgba(124, 58, 237, 0.45);
}

.ai-float-btn svg {
    width: 26px;
    height: 26px;
    transition: transform 0.2s ease;
}

.ai-float-btn.is-open svg {
    transform: rotate(90deg);
}

/* 悬浮AI助手 - 面板 */
.ai-float-panel {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 400px;
    height: 550px;
    background: var(--color-bg-base, #fff);
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: var(--radius-xl, 18px);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.03);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(16px) scale(0.96);
    transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
}

.ai-float-panel.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

/* 面板顶栏 */
.ai-float-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--color-border, #e2e8f0);
    background: var(--color-bg-base, #fff);
    flex-shrink: 0;
}

.ai-float-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ai-float-header-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #f97316);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-float-header-avatar svg {
    width: 16px;
    height: 16px;
    stroke: #fff;
}

.ai-float-header-info h3 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-foreground, #0f172a);
    margin: 0;
    line-height: 1.2;
}

.ai-float-header-info p {
    font-size: 0.6875rem;
    color: var(--color-foreground-dim, #94a3b8);
    margin: 0;
    line-height: 1.2;
}

.ai-float-close {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-foreground-muted, #64748b);
    transition: background 0.15s ease, color 0.15s ease;
}

.ai-float-close:hover {
    background: var(--color-bg-hover, #f1f5f9);
    color: var(--color-foreground, #0f172a);
}

.ai-float-close svg {
    width: 18px;
    height: 18px;
}

.ai-float-header-actions {
    display: flex;
    align-items: center;
    gap: 2px;
}

.ai-float-clear {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-foreground-muted, #64748b);
    transition: background 0.15s ease, color 0.15s ease;
}

.ai-float-clear:hover {
    background: #fef2f2;
    color: #ef4444;
}

body.dark .ai-float-clear:hover {
    background: #451a1a;
    color: #f87171;
}

.ai-float-clear svg {
    width: 16px;
    height: 16px;
}

/* 消息区域 */
.ai-float-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    scroll-behavior: smooth;
}

.ai-float-msg {
    display: flex;
    margin-bottom: 16px;
    animation: aiFloatFadeIn 0.3s ease;
}

.ai-float-msg--user {
    justify-content: flex-end;
}

.ai-float-msg--ai {
    justify-content: flex-start;
}

@keyframes aiFloatFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.ai-float-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.875rem;
    line-height: 1.6;
    word-break: break-word;
}

.ai-float-msg--user .ai-float-bubble {
    background: #7c3aed;
    color: #fff;
    border-bottom-right-radius: 4px;
}

.ai-float-msg--ai .ai-float-bubble {
    background: var(--color-bg-surface, #f8fafc);
    color: var(--color-foreground, #0f172a);
    border-bottom-left-radius: 4px;
}

.ai-float-bubble p { margin: 0 0 6px; }
.ai-float-bubble p:last-child { margin-bottom: 0; }

.ai-float-bubble h2,
.ai-float-bubble h3,
.ai-float-bubble h4 {
    color: var(--color-foreground, #0f172a);
    margin: 8px 0 4px;
    line-height: 1.4;
}

.ai-float-bubble h2 { font-size: 1rem; font-weight: 700; }
.ai-float-bubble h3 { font-size: 0.9375rem; font-weight: 600; }
.ai-float-bubble h4 { font-size: 0.875rem; font-weight: 600; }

.ai-float-code-wrap {
    position: relative;
    margin: 8px 0;
    border-radius: 8px;
    overflow: hidden;
    background: #1e293b;
}

.ai-float-code-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 12px;
    background: #0f172a;
    font-size: 0.6875rem;
    color: #94a3b8;
}

.ai-float-code-copy {
    background: none;
    border: 1px solid #475569;
    color: #94a3b8;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.6875rem;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}

.ai-float-code-copy:hover {
    background: #334155;
    color: #e2e8f0;
    border-color: #64748b;
}

.ai-float-code-copy.copied {
    background: #166534;
    color: #fff;
    border-color: #166534;
}

.ai-float-code-wrap pre {
    margin: 0;
    padding: 12px;
    background: #1e293b;
    color: #e2e8f0;
    overflow: hidden;
    font-size: 0.8125rem;
    line-height: 1.6;
    max-height: 200px;
    transition: max-height 0.3s ease;
}

.ai-float-code-wrap.expanded pre {
    max-height: none;
    overflow: auto;
}

.ai-float-code-toggle {
    width: 100%;
    padding: 8px;
    background: #0f172a;
    color: #94a3b8;
    border: none;
    border-top: 1px solid #334155;
    font-size: 0.75rem;
    cursor: pointer;
    transition: color 0.15s;
    font-family: inherit;
}

.ai-float-code-toggle:hover {
    color: #e2e8f0;
    background: #1e293b;
}

.ai-float-code-wrap code {
    background: none;
    padding: 0;
    font-size: 0.8125rem;
}

.ai-float-bubble code {
    background: rgba(0,0,0,0.06);
    padding: 2px 5px;
    border-radius: 4px;
    font-size: 0.8125rem;
}

/* 欢迎界面 */
.ai-float-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 20px;
    text-align: center;
}

.ai-float-welcome-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #f97316);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.ai-float-welcome-icon svg {
    width: 28px;
    height: 28px;
    stroke: #fff;
}

.ai-float-welcome h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-foreground, #0f172a);
    margin: 0 0 6px;
}

.ai-float-welcome p {
    font-size: 0.8125rem;
    color: var(--color-foreground-muted, #64748b);
    margin: 0 0 20px;
    line-height: 1.5;
}

.ai-float-suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    max-width: 280px;
}

.ai-float-suggestion {
    padding: 10px 14px;
    background: var(--color-bg-base, #fff);
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 10px;
    text-align: left;
    cursor: pointer;
    font-size: 0.8125rem;
    color: var(--color-foreground, #0f172a);
    transition: border-color 0.15s ease, background 0.15s ease;
}

.ai-float-suggestion:hover {
    border-color: #7c3aed;
    background: #f5f3ff;
}

/* 打字指示器 */
.ai-float-typing {
    display: flex;
    gap: 4px;
    padding: 4px 0;
}

.ai-float-typing span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
    animation: aiFloatTyping 1.2s infinite;
}

.ai-float-typing span:nth-child(2) { animation-delay: 0.2s; }
.ai-float-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes aiFloatTyping {
    0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
    30% { opacity: 1; transform: translateY(-4px); }
}

/* 输入区域 */
.ai-float-input-area {
    padding: 10px 12px;
    border-top: 1px solid var(--color-border, #e2e8f0);
    background: var(--color-bg-base, #fff);
    flex-shrink: 0;
    position: relative;
}

.ai-float-input-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.ai-float-input-middle {
    flex: 1;
    min-width: 0;
    background: var(--color-bg-surface, #f8fafc);
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 12px;
    padding: 4px 8px 4px 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.ai-float-input-middle:focus-within {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.ai-float-text-area {
    display: flex;
}

.ai-float-voice-area {
    display: flex;
    padding: 0;
}

.ai-float-input {
    flex: 1;
    border: none;
    background: none;
    outline: none;
    font-size: 0.875rem;
    line-height: 1.5;
    resize: none;
    max-height: 80px;
    padding: 4px 0;
    color: var(--color-foreground, #0f172a);
}

.ai-float-send {
    width: 34px;
    height: 42px;
    border-radius: 10px;
    background: #7c3aed;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: opacity 0.15s ease, background 0.15s ease;
}

.ai-float-send:hover {
    background: #6d28d9;
}

.ai-float-send:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.ai-float-send svg {
    width: 16px;
    height: 16px;
}

/* 移动端全屏 */
@media (max-width: 768px) {
    .ai-float-btn {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
    }

    .ai-float-btn svg {
        width: 22px;
        height: 22px;
    }

    .ai-float-panel {
        inset: 0;
        width: 100%;
        height: 100%;
        bottom: 0;
        right: 0;
        border-radius: 0;
        border: none;
    }
}

/* 暗色模式 */
body.dark .ai-float-btn {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

body.dark .ai-float-panel {
    background: #1e293b;
    border-color: #334155;
}

body.dark .ai-float-header {
    background: #1e293b;
    border-color: #334155;
}

body.dark .ai-float-header-info h3 { color: #f1f5f9; }

body.dark .ai-float-msg--ai .ai-float-bubble {
    background: #334155;
    color: #e2e8f0;
}

body.dark .ai-float-input-area {
    background: #1e293b;
    border-color: #334155;
}

body.dark .ai-float-input-middle {
    background: #334155;
    border-color: #475569;
}

body.dark .ai-float-input {
    color: #e2e8f0;
}

body.dark .ai-float-suggestion {
    background: #334155;
    border-color: #475569;
    color: #e2e8f0;
}

body.dark .ai-float-suggestion:hover {
    background: #3b4c6b;
    border-color: #8b5cf6;
}

body.dark .ai-float-close:hover {
    background: #334155;
}

/* 邮件表单 */
.ai-float-email-form {
    margin-top: 10px;
    background: var(--color-bg-base, #fff);
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 10px;
    padding: 14px;
}

body.dark .ai-float-email-form {
    background: #334155;
    border-color: #475569;
}

.ai-float-email-form h4 {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--color-foreground, #0f172a);
    margin: 0 0 10px;
}

.ai-float-email-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 8px;
}

.ai-float-email-field label {
    font-size: 0.75rem;
    color: var(--color-foreground-muted, #64748b);
    font-weight: 500;
}

.ai-float-email-field input,
.ai-float-email-field textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 8px;
    font-size: 0.8125rem;
    background: var(--color-bg-surface, #f8fafc);
    color: var(--color-foreground, #0f172a);
    outline: none;
    transition: border-color 0.15s ease;
    box-sizing: border-box;
}

body.dark .ai-float-email-field input,
body.dark .ai-float-email-field textarea {
    background: #1e293b;
    border-color: #475569;
    color: #e2e8f0;
}

.ai-float-email-field input:focus,
.ai-float-email-field textarea:focus {
    border-color: #7c3aed;
}

.ai-float-email-field textarea {
    resize: vertical;
    min-height: 60px;
    font-family: inherit;
}

.ai-float-email-submit {
    width: 100%;
    padding: 8px;
    background: #7c3aed;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s ease;
}

.ai-float-email-submit:hover {
    background: #6d28d9;
}

.ai-float-email-submit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ai-float-email-success {
    padding: 10px;
    background: #dcfce7;
    color: #166534;
    border-radius: 8px;
    font-size: 0.8125rem;
    text-align: center;
    margin-top: 8px;
}

body.dark .ai-float-email-success {
    background: #166534;
    color: #dcfce7;
}

/* ===== 语音模式切换按钮 ===== */
.ai-float-toggle {
    width: 34px;
    height: 42px;
    border-radius: 10px;
    background: var(--color-bg-surface, #f8fafc);
    border: 1px solid var(--color-border, #e2e8f0);
    color: var(--color-foreground-muted, #64748b);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
}

.ai-float-toggle:hover {
    background: #f5f3ff;
    border-color: #7c3aed;
    color: #7c3aed;
}

.ai-float-toggle.active {
    background: #7c3aed;
    border-color: #7c3aed;
    color: #fff;
}

.ai-float-toggle svg { width: 18px; height: 18px; }

/* ===== + 号按钮 ===== */
.ai-float-plus {
    width: 34px;
    height: 42px;
    border-radius: 10px;
    background: var(--color-bg-surface, #f8fafc);
    border: 1px solid var(--color-border, #e2e8f0);
    color: var(--color-foreground-muted, #64748b);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
}

.ai-float-plus:hover {
    background: #f5f3ff;
    border-color: #7c3aed;
    color: #7c3aed;
}

.ai-float-plus svg { width: 20px; height: 20px; }

.ai-float-plus.stop-mode {
    background: #ef4444 !important;
    border-color: #ef4444 !important;
    color: #fff !important;
}

.ai-float-plus.stop-mode:hover {
    background: #dc2626 !important;
}

/* ===== + 号弹出菜单 ===== */
.ai-float-plus-menu {
    position: absolute;
    bottom: 60px;
    left: 0;
    right: 0;
    background: #fff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    padding: 16px;
    display: none;
    gap: 20px;
    justify-content: center;
    z-index: 8;
}

.ai-float-plus-menu.active {
    display: flex;
}

.ai-float-plus-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: opacity 0.15s;
}

.ai-float-plus-menu-item:hover {
    opacity: 0.8;
}

.ai-float-plus-menu-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-float-plus-menu-icon svg {
    width: 24px;
    height: 24px;
}

.ai-float-plus-menu-item span {
    font-size: 0.75rem;
    color: var(--color-foreground-muted, #64748b);
}

/* ===== 按住说话按钮 ===== */
.ai-float-hold-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    height: 34px;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    transition: all 0.15s ease;
    padding: 0;
}

.ai-float-hold-btn:active,
.ai-float-hold-btn.pressing {
    opacity: 0.7;
}

.ai-float-hold-btn-text {
    font-size: 15px;
    font-weight: 500;
    color: #374151;
    letter-spacing: 4px;
    pointer-events: none;
}

/* ===== 录音浮层 ===== */
.ai-voice-overlay {
    position: absolute;
    bottom: 64px;
    left: 50%;
    transform: translateX(-50%);
    width: 160px;
    padding: 20px 16px;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    border-radius: 16px;
    display: none;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 10;
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.35);
    animation: aiVoiceOverlayIn 0.15s ease;
}

.ai-voice-overlay.active { display: flex; }

.ai-voice-overlay.cancel {
    background: linear-gradient(135deg, #ef4444, #f87171);
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
}

@keyframes aiVoiceOverlayIn {
    from { opacity: 0; transform: translateX(-50%) translateY(10px) scale(0.9); }
    to { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
}

.ai-voice-overlay .ai-voice-mic-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-voice-overlay .ai-voice-mic-icon svg {
    width: 24px;
    height: 24px;
    color: #fff;
}

.ai-voice-overlay .ai-voice-wave {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    height: 28px;
}

.ai-voice-overlay .ai-voice-wave-bar {
    width: 3px;
    height: 6px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 2px;
    animation: aiVoiceWaveBar 0.6s ease-in-out infinite;
}

.ai-voice-overlay .ai-voice-wave-bar:nth-child(1) { animation-delay: 0s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(2) { animation-delay: 0.08s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(3) { animation-delay: 0.16s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(4) { animation-delay: 0.24s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(5) { animation-delay: 0.32s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(6) { animation-delay: 0.24s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(7) { animation-delay: 0.16s; }
.ai-voice-overlay .ai-voice-wave-bar:nth-child(8) { animation-delay: 0.08s; }

@keyframes aiVoiceWaveBar {
    0%, 100% { height: 5px; opacity: 0.4; }
    50% { height: 22px; opacity: 1; }
}

.ai-voice-overlay .ai-voice-timer {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    font-variant-numeric: tabular-nums;
}

.ai-voice-overlay .ai-voice-hint {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

/* ===== 半透明遮罩 ===== */
.ai-voice-backdrop {
    position: absolute;
    top: 56px;
    left: 0;
    right: 0;
    bottom: 60px;
    background: rgba(0, 0, 0, 0.08);
    z-index: 5;
    display: none;
    cursor: pointer;
}

.ai-voice-backdrop.active { display: block; }

/* 语音移动端适配 */
@media (max-width: 768px) {
    .ai-voice-overlay {
        width: 150px;
        padding: 16px 14px;
    }
    .ai-float-hold-btn { height: 44px; }
}
</style>

<div class="ai-float-btn" id="aiFloatBtn" title="AI 助手">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
    </svg>
</div>

<div class="ai-float-panel" id="aiFloatPanel">
    <div class="ai-float-header">
        <div class="ai-float-header-left">
            <div class="ai-float-header-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a4 4 0 014 4c0 1.95-1.4 3.58-3.25 3.93L12 22"/>
                    <path d="M12 2a4 4 0 00-4 4c0 1.95 1.4 3.58 3.25 3.93"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                </svg>
            </div>
            <div class="ai-float-header-info">
                <h3>Aiphoto AI 助手</h3>
                <p>随时为你解答</p>
            </div>
        </div>
        <div class="ai-float-header-actions">
            <button class="ai-float-clear" id="aiFloatClear" title="清空聊天记录">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                </svg>
            </button>
            <button class="ai-float-close" id="aiFloatClose" title="关闭">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="ai-float-messages" id="aiFloatMessages">
        <div class="ai-float-welcome" id="aiFloatWelcome">
            <div class="ai-float-welcome-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <h4>你好，有什么想聊的？</h4>
            <p>我可以帮你写文案、回答问题、提供创作灵感</p>
            <div class="ai-float-suggestions">
                <button class="ai-float-suggestion" data-prompt="帮我写一段关于海边日落的描述">写一段海边日落描述</button>
                <button class="ai-float-suggestion" data-prompt="解释一下什么是 AI 绘画">什么是 AI 绘画</button>
                <button class="ai-float-suggestion" data-prompt="给我一些图片创作的灵感">给我创作灵感</button>
                <button class="ai-float-suggestion ai-float-suggestion--random" data-prompt="">优化提示词</button>
                <button class="ai-float-suggestion" data-prompt="我想删除一张图片">删除图片</button>
            </div>
        </div>
    </div>

    <div class="ai-float-input-area" id="aiFloatInputArea">
        <div class="ai-float-input-row">
            <!-- 左侧：模式切换按钮 -->
            <button class="ai-float-toggle" id="aiFloatToggleVoice" title="切换到语音输入">
                <svg class="icon-mic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="23"/>
                    <line x1="8" y1="23" x2="16" y2="23"/>
                </svg>
                <svg class="icon-keyboard" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <line x1="6" y1="8" x2="6" y2="8.01"/><line x1="10" y1="8" x2="10" y2="8.01"/>
                    <line x1="14" y1="8" x2="14" y2="8.01"/><line x1="18" y1="8" x2="18" y2="8.01"/>
                    <line x1="6" y1="12" x2="6" y2="12.01"/><line x1="10" y1="12" x2="10" y2="12.01"/>
                    <line x1="14" y1="12" x2="14" y2="12.01"/><line x1="18" y1="12" x2="18" y2="12.01"/>
                    <line x1="8" y1="16" x2="16" y2="16"/>
                </svg>
            </button>
            <!-- 中间：文字输入 / 按住说话 -->
            <div class="ai-float-input-middle">
                <div class="ai-float-text-area" id="aiFloatTextMode">
                    <textarea class="ai-float-input" id="aiFloatInput" rows="1" placeholder="输入你的问题..."></textarea>
                </div>
                <div class="ai-float-voice-area" id="aiFloatVoiceMode" style="display:none;">
                    <button class="ai-float-hold-btn" id="aiFloatHoldBtn">
                        <span class="ai-float-hold-btn-text">按住 说话</span>
                    </button>
                </div>
            </div>
            <!-- 右侧：发送按钮（文字模式）+ 加号按钮（始终显示） -->
            <button class="ai-float-send" id="aiFloatSend" disabled title="发送">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
            <button class="ai-float-plus" id="aiFloatPlus" title="更多功能">
                <svg class="icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <svg class="icon-stop" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                    <rect x="6" y="6" width="12" height="12" rx="2"/>
                </svg>
            </button>
        </div>
        <!-- 加号按钮弹出菜单 -->
        <div class="ai-float-plus-menu" id="aiFloatPlusMenu">
            <div class="ai-float-plus-menu-item" data-action="photo">
                <div class="ai-float-plus-menu-icon" style="background:#7c3aed;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <span>上传图片</span>
            </div>
            <div class="ai-float-plus-menu-item" data-action="file">
                <div class="ai-float-plus-menu-icon" style="background:#f97316;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <span>上传文件</span>
            </div>
        </div>
        <!-- 录音浮层 -->
        <div class="ai-voice-overlay" id="aiVoiceOverlay">
            <div class="ai-voice-mic-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                </svg>
            </div>
            <div class="ai-voice-wave">
                <div class="ai-voice-wave-bar"></div><div class="ai-voice-wave-bar"></div>
                <div class="ai-voice-wave-bar"></div><div class="ai-voice-wave-bar"></div>
                <div class="ai-voice-wave-bar"></div><div class="ai-voice-wave-bar"></div>
                <div class="ai-voice-wave-bar"></div><div class="ai-voice-wave-bar"></div>
            </div>
            <div class="ai-voice-timer" id="aiVoiceTimer">0:00</div>
            <div class="ai-voice-hint" id="aiVoiceHint">松开 发送</div>
        </div>
        <div class="ai-voice-backdrop" id="aiVoiceBackdrop"></div>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('aiFloatBtn');
    var panel = document.getElementById('aiFloatPanel');
    var closeBtn = document.getElementById('aiFloatClose');
    var clearBtn = document.getElementById('aiFloatClear');
    var messages = document.getElementById('aiFloatMessages');
    var input = document.getElementById('aiFloatInput');
    var sendBtn = document.getElementById('aiFloatSend');
    var isOpen = false;
    var isGenerating = false;
    var chatAbortController = null;
    var conversationHistory = [];
    var STORAGE_KEY = 'aiphoto_float_chat';

    // ========== 预设回复规则 ==========
    var PRESET_RULES = [
        {
            match: function(msg) { return /删[除掉]|移除|去掉|remove|delete/i.test(msg); },
            reply: '如需删除图片，请发送邮件至管理员邮箱，告知您要删除的图片链接或描述，我们会尽快处理。\n\n您也可以直接在下方填写表单发送：',
            showForm: true,
            formType: 'delete'
        },
        {
            match: function(msg) { return /版权|侵权|投诉|举报|copyright/i.test(msg); },
            reply: '如果您认为某张图片涉及版权问题，请发送邮件至管理员邮箱，附上相关说明，我们会认真核实并处理。\n\n您也可以直接在下方填写表单发送：',
            showForm: true,
            formType: 'report'
        },
        {
            match: function(msg) { return /联系|客服|人工|反馈|意见|contact|support/i.test(msg); },
            reply: '您可以通过以下方式联系我们：\n\n1. 邮件反馈：使用下方表单直接发送\n2. 网站反馈：在页面底部找到联系我们\n\n我们会尽快回复您！',
            showForm: true,
            formType: 'contact'
        },
        {
            match: function(msg) { return /价格|收费|免费|会员|VIP|付费|多少钱/i.test(msg); },
            reply: 'Aiphoto 目前提供免费的 AI 图片生成服务！每天都有一定的免费额度可以使用。\n\n如需了解更多，请访问网站查看最新活动。',
            showForm: false
        },
        {
            match: function(msg) { return /怎么用|如何使用|使用方法|教程|帮助|guide|help|how to use/i.test(msg); },
            reply: '使用方法很简单：\n\n1. 点击"文生图"输入描述文字\n2. 选择效果、镜头角度等参数\n3. 点击生成即可获得 AI 创作的图片\n\n您也可以点击页面上的各个功能模块探索更多！',
            showForm: false
        }
    ];

    function checkPresetReply(msg) {
        for (var i = 0; i < PRESET_RULES.length; i++) {
            if (PRESET_RULES[i].match(msg)) return PRESET_RULES[i];
        }
        return null;
    }

    function getEmailFormHTML(type) {
        var titles = { delete: '图片删除申请', report: '版权投诉', contact: '联系我们' };
        var subjects = { delete: '图片删除申请', report: '版权投诉', contact: '用户反馈' };
        var placeholders = { delete: '请描述要删除的图片（链接或描述）', report: '请描述涉及版权问题的图片', contact: '请输入您的问题或建议' };
        return '<div class="ai-float-email-form" data-email-form="' + type + '">' +
            '<h4>' + (titles[type] || '发送邮件') + '</h4>' +
            '<div class="ai-float-email-field"><label>您的姓名</label><input type="text" data-field="name" placeholder="请输入姓名"></div>' +
            '<div class="ai-float-email-field"><label>您的邮箱</label><input type="email" data-field="email" placeholder="请输入邮箱地址"></div>' +
            '<div class="ai-float-email-field"><label>详细描述</label><textarea data-field="msg" rows="3" placeholder="' + (placeholders[type] || '请输入内容') + '"></textarea></div>' +
            '<button class="ai-float-email-submit" data-email-submit="' + type + '" data-subject="' + (subjects[type] || '用户反馈') + '">发送邮件</button>' +
        '</div>';
    }

    // ========== 工具函数 ==========
    function addMsg(text, type, animate) {
        var div = document.createElement('div');
        div.className = 'ai-float-msg ai-float-msg--' + type;
        if (!animate) div.style.animation = 'none';
        div.innerHTML = '<div class="ai-float-bubble">' + formatText(text) + '</div>';
        messages.appendChild(div);
        scrollToBottom();
    }

    function addRawHTML(html) {
        var wrapper = document.createElement('div');
        wrapper.className = 'ai-float-msg ai-float-msg--ai';
        wrapper.innerHTML = '<div class="ai-float-bubble">' + html + '</div>';
        messages.appendChild(wrapper);
        scrollToBottom();
    }

    function addLoading() {
        var id = 'aifl-' + Date.now();
        var div = document.createElement('div');
        div.className = 'ai-float-msg ai-float-msg--ai';
        div.id = id;
        div.innerHTML = '<div class="ai-float-bubble"><div class="ai-float-typing"><span></span><span></span><span></span></div></div>';
        messages.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeLoading(id) {
        var el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    var codeIdCounter = 0;

    function formatText(text) {
        text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        // 1. 处理完整闭合的代码块 ```...```
        text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, function(match, lang, code) {
            var id = 'codeblock-' + (++codeIdCounter);
            var langLabel = lang || 'code';
            var lines = code.trim().split('\n').length;
            var needToggle = lines > 8;
            return '<div class="ai-float-code-wrap' + (needToggle ? '' : ' expanded') + '" id="' + id + '">' +
                '<div class="ai-float-code-header"><span>' + langLabel + '</span>' +
                '<button class="ai-float-code-copy" data-code="' + id + '">复制</button></div>' +
                '<pre><code>' + code.trim() + '</code></pre>' +
                (needToggle ? '<button class="ai-float-code-toggle" data-toggle="' + id + '">▼ 查看全部</button>' : '') +
                '</div>';
        });

        // 2. 处理未闭合的代码块（AI长回复被截断的情况）
        var pendingCodeMatch = text.match(/```(\w*)\n?([\s\S]*?)$/);
        if (pendingCodeMatch && pendingCodeMatch[2].trim().length > 0) {
            var id = 'codeblock-' + (++codeIdCounter);
            var langLabel = pendingCodeMatch[1] || 'code';
            var codeContent = pendingCodeMatch[2].trim();
            var lines = codeContent.split('\n').length;
            var needToggle = lines > 8;
            text = text.replace(/```(\w*)\n?[\s\S]*$/, '<div class="ai-float-code-wrap' + (needToggle ? '' : ' expanded') + '" id="' + id + '">' +
                '<div class="ai-float-code-header"><span>' + langLabel + '</span>' +
                '<button class="ai-float-code-copy" data-code="' + id + '">复制</button></div>' +
                '<pre><code>' + codeContent + '</code></pre>' +
                (needToggle ? '<button class="ai-float-code-toggle" data-toggle="' + id + '">▼ 查看全部</button>' : '') +
                '</div>');
        }

        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/^### (.+)$/gm, '<h4>$1</h4>');
        text = text.replace(/^## (.+)$/gm, '<h3>$1</h3>');
        text = text.replace(/^# (.+)$/gm, '<h2>$1</h2>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    // 事件委托：复制 + 展开收起
    messages.addEventListener('click', function(e) {
        // 复制按钮
        var copyBtn = e.target.closest('.ai-float-code-copy');
        if (copyBtn) {
            e.stopPropagation();
            var codeId = copyBtn.getAttribute('data-code');
            var wrap = document.getElementById(codeId);
            if (!wrap) return;
            var codeEl = wrap.querySelector('code');
            var code = codeEl.innerText || codeEl.textContent;
            // 还原 HTML 实体
            var temp = document.createElement('textarea');
            temp.innerHTML = code.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            code = temp.value;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function() {
                    copyBtn.textContent = '已复制';
                    copyBtn.classList.add('copied');
                    setTimeout(function() { copyBtn.textContent = '复制'; copyBtn.classList.remove('copied'); }, 2000);
                }).catch(function() { fallbackCopy(code, copyBtn); });
            } else {
                fallbackCopy(code, copyBtn);
            }
            return;
        }
        // 展开/收起
        var toggleBtn = e.target.closest('.ai-float-code-toggle');
        if (toggleBtn) {
            e.stopPropagation();
            var toggleId = toggleBtn.getAttribute('data-toggle');
            var toggleWrap = document.getElementById(toggleId);
            if (!toggleWrap) return;
            var isExpanded = toggleWrap.classList.toggle('expanded');
            toggleBtn.textContent = isExpanded ? '▲ 收起' : '▼ 查看全部';
            return;
        }
    });

    function fallbackCopy(text, btn) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;left:0;top:0;width:1px;height:1px;padding:0;border:none;outline:none;box-shadow:none;opacity:0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            var ok = document.execCommand('copy');
            btn.textContent = ok ? '已复制' : '复制失败';
            if (ok) btn.classList.add('copied');
        } catch (e) {
            btn.textContent = '复制失败';
        }
        document.body.removeChild(ta);
        setTimeout(function() { btn.textContent = '复制'; btn.classList.remove('copied'); }, 2000);
    }

    function saveHistory() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(conversationHistory.slice(-20)));
    }

    // ========== 面板开关 ==========
    btn.addEventListener('click', function() {
        isOpen = !isOpen;
        panel.classList.toggle('is-open', isOpen);
        btn.classList.toggle('is-open', isOpen);
        if (isOpen) { input.focus(); scrollToBottom(); }
    });

    closeBtn.addEventListener('click', function() {
        isOpen = false;
        panel.classList.remove('is-open');
        btn.classList.remove('is-open');
    });

    // 聊天框内滚动时不滚动页面
    messages.addEventListener('wheel', function(e) {
        var atTop = messages.scrollTop === 0 && e.deltaY < 0;
        var atBottom = messages.scrollTop + messages.clientHeight >= messages.scrollHeight - 2 && e.deltaY > 0;
        if (atTop || atBottom) {
            e.preventDefault();
        }
    }, { passive: false });

    // 清空聊天记录
    clearBtn.addEventListener('click', function() {
        if (!confirm('确定要清空聊天记录吗？')) return;
        conversationHistory = [];
        localStorage.removeItem(STORAGE_KEY);
        messages.innerHTML = '';
        // 恢复欢迎界面
        messages.innerHTML = '<div class="ai-float-welcome" id="aiFloatWelcome">' +
            '<div class="ai-float-welcome-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>' +
            '<h4>你好，有什么想聊的？</h4>' +
            '<p>我可以帮你写文案、回答问题、提供创作灵感</p>' +
            '<div class="ai-float-suggestions">' +
            '<button class="ai-float-suggestion" data-prompt="帮我写一段关于海边日落的描述">写一段海边日落描述</button>' +
            '<button class="ai-float-suggestion" data-prompt="解释一下什么是 AI 绘画">什么是 AI 绘画</button>' +
            '<button class="ai-float-suggestion" data-prompt="给我一些图片创作的灵感">给我创作灵感</button>' +
            '<button class="ai-float-suggestion ai-float-suggestion--random" data-prompt="">优化提示词</button>' +
            '<button class="ai-float-suggestion" data-prompt="我想删除一张图片">删除图片</button>' +
            '</div></div>';
    });

    // 点击外部关闭
    document.addEventListener('click', function(e) {
        // 直接读取面板 class 判断，不依赖 isOpen 变量
        if (!panel.classList.contains('is-open')) return;
        if (panel.contains(e.target) || btn.contains(e.target)) return;
        isOpen = false;
        panel.classList.remove('is-open');
        btn.classList.remove('is-open');
    });

    // ========== 随机提示词库 ==========
    var RANDOM_PROMPTS = [
        '一只穿着太空服的柴犬在月球上散步，背景是地球',
        '赛博朋克风格的东京街头，霓虹灯倒映在雨水中',
        '水墨风格的中国山水画，远处有一座古桥',
        '蒸汽朋克风格的飞行器飞越维多利亚时代的城市',
        '一只巨大的鲸鱼游过云层之上，下方是微缩城市',
        '梵高星空风格的向日葵花田',
        '像素风格的中世纪城堡和龙',
        '水彩风格的京都竹林小径，阳光透过竹叶',
        '未来都市的天际线，飞行汽车穿梭其中',
        '一只戴着墨镜的猫咪坐在迈阿密海滩的躺椅上'
    ];

    // ========== 事件委托：建议按钮 + 邮件表单 ==========
    messages.addEventListener('click', function(e) {
        // 快捷建议
        var suggestion = e.target.closest('.ai-float-suggestion');
        if (suggestion) {
            e.stopPropagation();
            var prompt = suggestion.getAttribute('data-prompt');
            // 随机提示词按钮
            if (suggestion.classList.contains('ai-float-suggestion--random') || !prompt) {
                prompt = RANDOM_PROMPTS[Math.floor(Math.random() * RANDOM_PROMPTS.length)];
            }
            input.value = prompt;
            input.dispatchEvent(new Event('input'));
            sendMsg();
            return;
        }
        var submitBtn = e.target.closest('[data-email-submit]');
        if (!submitBtn) return;

        e.preventDefault();
        e.stopPropagation();

        var form = submitBtn.closest('[data-email-form]');
        if (!form) return;

        var name = form.querySelector('[data-field="name"]').value.trim();
        var email = form.querySelector('[data-field="email"]').value.trim();
        var msg = form.querySelector('[data-field="msg"]').value.trim();
        var subject = submitBtn.getAttribute('data-subject');

        if (!name || !email || !msg) {
            alert('请填写完整信息');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = '发送中...';

        fetch(aiphotoAjax.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=aiphoto_send_email&nonce=' + encodeURIComponent(aiphotoAjax.nonce) +
                  '&name=' + encodeURIComponent(name) +
                  '&email=' + encodeURIComponent(email) +
                  '&subject=' + encodeURIComponent(subject) +
                  '&message=' + encodeURIComponent(msg)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                addMsg('邮件已发送成功！我们会尽快处理您的请求。', 'ai', true);
                conversationHistory.push({ role: 'assistant', content: '邮件已发送成功！我们会尽快处理您的请求。' });
                form.remove();
            } else {
                addMsg('发送失败：' + (data.data.message || '请稍后重试'), 'ai', true);
                submitBtn.disabled = false;
                submitBtn.textContent = '发送邮件';
            }
        })
        .catch(function() {
            addMsg('网络错误，请稍后重试。', 'ai', true);
            submitBtn.disabled = false;
            submitBtn.textContent = '发送邮件';
        });
    });

    // ========== 输入 & 发送 ==========
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        sendBtn.disabled = this.value.trim().length === 0 || isGenerating;
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) sendMsg();
        }
    });

    sendBtn.addEventListener('click', sendMsg);

    function sendMsg() {
        var msg = input.value.trim();
        if (!msg || isGenerating) return;

        var welcome = document.getElementById('aiFloatWelcome');
        if (welcome) welcome.remove();

        addMsg(msg, 'user', true);
        conversationHistory.push({ role: 'user', content: msg });

        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        // 检查预设回复
        var preset = checkPresetReply(msg);
        if (preset) {
            addMsg(preset.reply, 'ai', true);
            conversationHistory.push({ role: 'assistant', content: preset.reply });
            if (preset.showForm) {
                setTimeout(function() { addRawHTML(getEmailFormHTML(preset.formType)); }, 300);
            }
            saveHistory();
            return;
        }

        isGenerating = true;
        plusBtn.classList.add('stop-mode');
        plusBtn.querySelector('.icon-plus').style.display = 'none';
        plusBtn.querySelector('.icon-stop').style.display = 'block';

        // 创建 AI 消息气泡（先显示加载动画，流式填充时替换）
        var aiDiv = document.createElement('div');
        aiDiv.className = 'ai-float-msg ai-float-msg--ai';
        aiDiv.innerHTML = '<div class="ai-float-bubble"><div class="ai-float-typing"><span></span><span></span><span></span></div></div>';
        messages.appendChild(aiDiv);
        var aiBubble = aiDiv.querySelector('.ai-float-bubble');
        scrollToBottom();

        var fullReply = '';
        var firstChunk = true;
        var abortController = new AbortController();
        chatAbortController = abortController;

        var streamUrl = aiphotoAjax.url + '?action=aiphoto_chat_stream&nonce=' +
            encodeURIComponent(aiphotoAjax.nonce) +
            '&message=' + encodeURIComponent(msg) +
            '&history=' + encodeURIComponent(JSON.stringify(conversationHistory.slice(-10)));

        fetch(streamUrl, { signal: abortController.signal })
            .then(function(response) {
                var reader = response.body.getReader();
                var decoder = new TextDecoder();
                var buffer = '';

                function read() {
                    return reader.read().then(function(result) {
                        if (result.done) return;
                        buffer += decoder.decode(result.value, { stream: true });
                        var lines = buffer.split('\n');
                        buffer = lines.pop();
                        for (var i = 0; i < lines.length; i++) {
                            var line = lines[i].trim();
                            if (line.indexOf('data: ') !== 0) continue;
                            var jsonStr = line.substring(6);
                            if (jsonStr === '[DONE]') {
                                finishStream(fullReply);
                                return;
                            }
                            try {
                                var data = JSON.parse(jsonStr);
                                if (data.error) {
                                    aiBubble.innerHTML = formatText(data.error);
                                    finishStream('');
                                    return;
                                }
                                if (data.text) {
                                    fullReply += data.text;
                                    if (firstChunk) {
                                        firstChunk = false;
                                        aiBubble.innerHTML = '';
                                    }
                                    aiBubble.innerHTML = formatText(fullReply);
                                    scrollToBottom();
                                }
                            } catch(e) {}
                        }
                        return read();
                    });
                }
                return read();
            })
            .catch(function(err) {
                if (err.name === 'AbortError') {
                    if (fullReply) { finishStream(fullReply); }
                    else { aiDiv.remove(); }
                    return;
                }
                aiBubble.innerHTML = formatText('网络错误，请检查网络连接后重试。');
                finishStream('');
            });

        function finishStream(reply) {
            isGenerating = false;
            plusBtn.classList.remove('stop-mode');
            plusBtn.querySelector('.icon-plus').style.display = 'block';
            plusBtn.querySelector('.icon-stop').style.display = 'none';
            chatAbortController = null;
            if (reply) {
                conversationHistory.push({ role: 'assistant', content: reply });
                playSendSound();
            }
            sendBtn.disabled = input.value.trim().length === 0;
            input.focus();
            saveHistory();
        }
    }

    // ========== 初始化 ==========
    function loadHistory() {
        try {
            var saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            if (saved.length > 0) {
                conversationHistory = saved;
                var welcome = document.getElementById('aiFloatWelcome');
                if (welcome) welcome.remove();
                saved.forEach(function(msg) {
                    addMsg(msg.content, msg.role === 'user' ? 'user' : 'ai', false);
                });
                scrollToBottom();
            }
        } catch (e) {}
    }

    // ========== 语音输入功能 ==========
    var textMode = document.getElementById('aiFloatTextMode');
    var voiceMode = document.getElementById('aiFloatVoiceMode');
    var toggleBtn = document.getElementById('aiFloatToggleVoice');
    var iconMic = toggleBtn.querySelector('.icon-mic');
    var iconKeyboard = toggleBtn.querySelector('.icon-keyboard');
    var holdBtn = document.getElementById('aiFloatHoldBtn');
    var voiceOverlay = document.getElementById('aiVoiceOverlay');
    var voiceTimerEl = document.getElementById('aiVoiceTimer');
    var voiceHintEl = document.getElementById('aiVoiceHint');
    var voiceBackdrop = document.getElementById('aiVoiceBackdrop');
    var plusBtn = document.getElementById('aiFloatPlus');
    var plusMenu = document.getElementById('aiFloatPlusMenu');

    var recognition = null;
    var recordingStartTime = 0;
    var recordingTimerInterval = null;
    var recordingSafetyTimer = null;
    var isRecording = false;
    var isCancelled = false;
    var userStopped = false;
    var touchStartY = 0;
    var voiceModeActive = false;
    var finalTranscript = '';

    // 模式切换（🎤 ↔ ⌨️）
    toggleBtn.addEventListener('click', function() {
        voiceModeActive = !voiceModeActive;
        if (voiceModeActive) {
            textMode.style.display = 'none';
            voiceMode.style.display = 'flex';
            sendBtn.style.display = 'none';
            iconMic.style.display = 'none';
            iconKeyboard.style.display = 'block';
        } else {
            voiceMode.style.display = 'none';
            textMode.style.display = 'flex';
            sendBtn.style.display = 'flex';
            iconMic.style.display = 'block';
            iconKeyboard.style.display = 'none';
            input.focus();
        }
        plusMenu.classList.remove('active');
    });

    // + 号按钮菜单
    plusBtn.addEventListener('click', function() {
        if (isGenerating && chatAbortController) {
            chatAbortController.abort();
            isGenerating = false;
            plusBtn.classList.remove('stop-mode');
            plusBtn.querySelector('.icon-plus').style.display = 'block';
            plusBtn.querySelector('.icon-stop').style.display = 'none';
            var loadingEl = messages.querySelector('.ai-float-typing');
            if (loadingEl) loadingEl.closest('.ai-float-msg').remove();
        } else {
            plusMenu.classList.toggle('active');
        }
    });

    // 点击其他区域关闭菜单
    document.addEventListener('click', function(e) {
        if (!plusBtn.contains(e.target) && !plusMenu.contains(e.target)) {
            plusMenu.classList.remove('active');
        }
    });

    // 菜单项点击
    var menuItems = plusMenu.querySelectorAll('.ai-float-plus-menu-item');
    for (var i = 0; i < menuItems.length; i++) {
        menuItems[i].addEventListener('click', function() {
            var action = this.getAttribute('data-action');
            plusMenu.classList.remove('active');
            if (action === 'photo') {
                // 触发图片上传
                var fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.onchange = function() {
                    if (this.files && this.files[0]) {
                        addMsg('已选择图片：' + this.files[0].name, 'user', true);
                    }
                };
                fileInput.click();
            } else if (action === 'file') {
                // 触发文件上传
                var fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.onchange = function() {
                    if (this.files && this.files[0]) {
                        addMsg('已选择文件：' + this.files[0].name, 'user', true);
                    }
                };
                fileInput.click();
            }
        });
    }

    // 遮罩点击取消录音
    voiceBackdrop.addEventListener('click', function() {
        if (isRecording) stopRecording(true);
    });

    // 按住说话 - 触摸事件
    holdBtn.addEventListener('touchstart', function(e) {
        e.preventDefault();
        if (isGenerating) return;
        touchStartY = e.touches[0].clientY;
        startRecording();
    });

    // touchmove 和 touchend 绑在 document 上，防止手指滑出按钮区域后事件丢失
    document.addEventListener('touchmove', function(e) {
        if (!isRecording) return;
        var dy = e.touches[0].clientY - touchStartY;
        if (dy < -60) {
            voiceOverlay.classList.add('cancel');
            voiceHintEl.textContent = '松开 取消';
        } else {
            voiceOverlay.classList.remove('cancel');
            voiceHintEl.textContent = '松开 发送';
        }
    }, { passive: true });

    document.addEventListener('touchend', function(e) {
        if (!isRecording) return;
        var cancelled = voiceOverlay.classList.contains('cancel');
        stopRecording(cancelled);
    });

    document.addEventListener('touchcancel', function(e) {
        if (!isRecording) return;
        stopRecording(true);
    });

    // 按住说话 - 鼠标事件
    holdBtn.addEventListener('mousedown', function(e) {
        e.preventDefault();
        if (isGenerating) return;
        touchStartY = e.clientY;
        startRecording();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isRecording) return;
        var dy = e.clientY - touchStartY;
        if (dy < -60) {
            voiceOverlay.classList.add('cancel');
            voiceHintEl.textContent = '松开 取消';
        } else {
            voiceOverlay.classList.remove('cancel');
            voiceHintEl.textContent = '松开 发送';
        }
    });

    document.addEventListener('mouseup', function(e) {
        if (!isRecording) return;
        var cancelled = voiceOverlay.classList.contains('cancel');
        stopRecording(cancelled);
    });

    // 发送音效（类似微信"嗖"的声音）
    function playSendSound() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1600, ctx.currentTime + 0.08);
            gain.gain.setValueAtTime(0.6, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.15);
            setTimeout(function() { ctx.close(); }, 200);
        } catch(e) {}
    }

    var mediaRecorder = null;
    var audioChunks = [];
    var useVosk = false;

    function getSupportedMimeType() {
        var types = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus', 'audio/mp4'];
        for (var i = 0; i < types.length; i++) { if (MediaRecorder.isTypeSupported(types[i])) return types[i]; }
        return '';
    }

    function startRecording() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            addMsg('您的浏览器不支持录音功能，请使用 Chrome 或 Edge 浏览器。', 'ai', true); return;
        }
        isCancelled = false; userStopped = false; audioChunks = [];
        var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SR && !useVosk) {
            startSpeechRecognition(SR);
        } else {
            startMediaRecorder();
        }
    }

    function startSpeechRecognition(SR) {
        recognition = new SR();
        recognition.lang = 'zh-CN';
        recognition.continuous = true;
        recognition.interimResults = false;

        function onResult(event) {
            var text = '';
            for (var i = 0; i < event.results.length; i++) {
                if (event.results[i].isFinal) {
                    text += event.results[i][0].transcript;
                }
            }
            if (text.trim()) { finalTranscript = text.trim(); }
        }

        function onEnd() {
            // 用户还没松开，自动重启
            if (isRecording && !userStopped) {
                try {
                    recognition = new SR();
                    recognition.lang = 'zh-CN';
                    recognition.continuous = true;
                    recognition.interimResults = false;
                    recognition.onresult = onResult;
                    recognition.onerror = onError;
                    recognition.onend = onEnd;
                    recognition.start();
                    return;
                } catch(e) {}
            }
            // 用户松开了，处理结果
            isRecording = false;
            if (isCancelled) return;
            if (finalTranscript.trim()) {
                playSendSound();
                var welcome = document.getElementById('aiFloatWelcome');
                if (welcome) welcome.remove();
                input.value = finalTranscript;
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 80) + 'px';
                sendBtn.disabled = false;
                sendMsg();
            } else {
                useVosk = true; startMediaRecorder();
            }
        }

        function onError(event) {
            if (event.error === 'no-speech' || event.error === 'aborted') return;
            useVosk = true; isRecording = false; startMediaRecorder();
        }

        recognition.onresult = onResult;
        recognition.onerror = onError;
        recognition.onend = onEnd;

        try {
            isRecording = true; recordingStartTime = Date.now();
            voiceOverlay.classList.add('active'); voiceBackdrop.classList.add('active');
            voiceHintEl.textContent = '松开 发送'; updateRecordingTimer();
            recognition.start();
            recordingSafetyTimer = setTimeout(function() {
                if (isRecording && !gotResult) {
                    try { recognition.stop(); } catch(e) {}
                    useVosk = true; isRecording = false; startMediaRecorder();
                }
            }, 8000);
        } catch(e) {
            useVosk = true; isRecording = false; startMediaRecorder();
        }
    }

    function startMediaRecorder() {
        navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream) {
            var mimeType = getSupportedMimeType();
            mediaRecorder = mimeType ? new MediaRecorder(stream, { mimeType: mimeType }) : new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function(e) { if (e.data.size > 0) audioChunks.push(e.data); };
            mediaRecorder.onstop = function() {
                stream.getTracks().forEach(function(t) { t.stop(); });
                if (!isCancelled && audioChunks.length > 0) {
                    var blob = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                    sendToVosk(blob);
                }
                isRecording = false;
                voiceOverlay.classList.remove('active', 'cancel');
                voiceBackdrop.classList.remove('active');
            };
            isRecording = true; recordingStartTime = Date.now();
            voiceOverlay.classList.add('active'); voiceBackdrop.classList.add('active');
            voiceHintEl.textContent = '松开 发送'; updateRecordingTimer();
            mediaRecorder.start();
            recordingSafetyTimer = setTimeout(function() { if (isRecording) stopRecording(false); }, 60000);
        }).catch(function() {
            addMsg('无法访问麦克风，请检查浏览器权限设置。', 'ai', true);
            voiceOverlay.classList.remove('active'); voiceBackdrop.classList.remove('active');
        });
    }

    function stopRecording(cancel) {
        isCancelled = cancel; userStopped = true;
        clearTimeout(recordingTimerInterval); clearTimeout(recordingSafetyTimer);
        voiceOverlay.classList.remove('active', 'cancel');
        voiceBackdrop.classList.remove('active');
        if (recognition) { try { recognition.stop(); } catch(e) {} recognition = null; }
        if (mediaRecorder && mediaRecorder.state === 'recording') { mediaRecorder.stop(); }
        else { isRecording = false; }
    }

    function updateRecordingTimer() {
        var e = Math.floor((Date.now() - recordingStartTime) / 1000);
        voiceTimerEl.textContent = Math.floor(e/60) + ':' + (e%60 < 10 ? '0' : '') + (e%60);
        if (isRecording) recordingTimerInterval = setTimeout(updateRecordingTimer, 200);
    }

    function sendToVosk(blob) {
        var reader = new FileReader();
        reader.onload = function() {
            var base64 = reader.result.split(',')[1];
            var loadingId = addLoading();
            fetch(aiphotoAjax.url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=aiphoto_recognize_speech&nonce=' + encodeURIComponent(aiphotoAjax.nonce) + '&audio=' + encodeURIComponent(base64)
            }).then(function(r) { return r.json(); }).then(function(data) {
                removeLoading(loadingId);
                if (data.success && data.data.text && data.data.text.trim()) {
                    playSendSound();
                    var welcome = document.getElementById('aiFloatWelcome');
                    if (welcome) welcome.remove();
                    input.value = data.data.text.trim();
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 80) + 'px';
                    sendBtn.disabled = false;
                    sendMsg();
                } else {
                    addMsg('未识别到语音内容，请重试。', 'ai', true);
                }
            }).catch(function() {
                removeLoading(loadingId);
                addMsg('语音识别服务连接失败，请稍后重试。', 'ai', true);
            });
        };
        reader.readAsDataURL(blob);
    }

    loadHistory();
})();

// 手机端：拦截 /chat 链接，直接打开悬浮聊天框
(function() {
    var isMobile = /Android|iPhone|iPad|iPod|Mobile|Windows Phone/i.test(navigator.userAgent);
    if (!isMobile) return;

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href]');
        if (!link) return;
        var href = link.getAttribute('href') || '';
        if (href.indexOf('/chat') === -1) return;

        e.preventDefault();
        e.stopPropagation();

        // 关闭汉堡菜单
        var nav = document.querySelector('.main-navigation');
        var toggle = document.querySelector('.menu-toggle');
        if (nav) nav.classList.remove('is-open');
        if (toggle) {
            toggle.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
        }

        // 打开悬浮聊天框
        var floatBtn = document.getElementById('aiFloatBtn');
        var floatPanel = document.getElementById('aiFloatPanel');
        if (floatBtn && floatPanel && !floatPanel.classList.contains('is-open')) {
            floatBtn.click();
        }

        // 如果当前就在 /chat 页面，返回上一页
        if (window.location.pathname.indexOf('/chat') !== -1) {
            if (history.length > 1) {
                history.back();
            } else {
                window.location.href = '<?php echo esc_js( home_url( '/' ) ); ?>';
            }
        }
    }, true);
})();
</script>

<?php wp_footer(); ?>
</body>
</html>