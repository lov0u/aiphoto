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
    padding: 12px;
    border-top: 1px solid var(--color-border, #e2e8f0);
    background: var(--color-bg-base, #fff);
    flex-shrink: 0;
}

.ai-float-input-wrap {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: var(--color-bg-surface, #f8fafc);
    border: 1px solid var(--color-border, #e2e8f0);
    border-radius: 12px;
    padding: 6px 8px 6px 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.ai-float-input-wrap:focus-within {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
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
    height: 34px;
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

body.dark .ai-float-input-wrap {
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

    <div class="ai-float-input-area">
        <div class="ai-float-input-wrap">
            <textarea class="ai-float-input" id="aiFloatInput" rows="1" placeholder="输入你的问题..."></textarea>
            <button class="ai-float-send" id="aiFloatSend" disabled title="发送">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
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
        var loadingId = addLoading();

        fetch(aiphotoAjax.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=aiphoto_chat&nonce=' + encodeURIComponent(aiphotoAjax.nonce) +
                  '&message=' + encodeURIComponent(msg) +
                  '&history=' + encodeURIComponent(JSON.stringify(conversationHistory.slice(-10)))
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            removeLoading(loadingId);
            if (data.success) {
                addMsg(data.data.reply, 'ai', true);
                conversationHistory.push({ role: 'assistant', content: data.data.reply });
            } else {
                addMsg(data.data.message || '抱歉，发生了错误，请重试。', 'ai', true);
            }
        })
        .catch(function() {
            removeLoading(loadingId);
            addMsg('网络错误，请检查网络连接后重试。', 'ai', true);
        })
        .finally(function() {
            isGenerating = false;
            sendBtn.disabled = input.value.trim().length === 0;
            input.focus();
            saveHistory();
        });
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