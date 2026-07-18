<?php
/**
 * Template Name: AI 聊天
 * Description: AIPhoto AI 智能聊天
 */

get_header();

$settings = aiphoto_get_settings();
?>
<script>document.body.classList.add('chat-page');</script>

<style>
/* 固定页面不滚动，但保留页脚给爬虫 */
html, body.chat-page {
    overflow: hidden;
    height: 100%;
}

/* 初始隐藏，避免闪烁 */
.chat-layout {
    visibility: hidden;
}

.chat-layout.ready {
    visibility: visible;
}

.chat-layout {
    display: flex;
    height: calc(100vh - 80px);
    margin-top: 80px;
    background: #fff;
    position: relative;
    padding: 0;
}

/* 左侧边栏（Agnes AI 风格） */
.chat-sidebar {
    width: 260px;
    background: transparent;
    border-right: none;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-collapse-btn {
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 150ms ease;
    flex-shrink: 0;
}

.sidebar-collapse-btn:hover {
    background: #e5e7eb;
    color: #111827;
}

.sidebar-nav {
    padding: 8px 12px;
}

.sidebar-nav-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: all 150ms ease;
    position: relative;
}

.sidebar-nav-item:hover {
    background: #e5e7eb;
}

.sidebar-nav-item--active {
    background: #eef2ff;
    color: #4f46e5;
    font-weight: 500;
}

/* 折叠状态下的悬停提示 */
.chat-sidebar.collapsed .sidebar-nav-item {
    position: relative;
}

.chat-sidebar.collapsed .sidebar-nav-item:hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #1f2937;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8125rem;
    white-space: nowrap;
    z-index: 9999;
    margin-left: 8px;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.chat-sidebar.collapsed .sidebar-footer-btn {
    position: relative;
}

.chat-sidebar.collapsed .sidebar-footer-btn:hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #1f2937;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8125rem;
    white-space: nowrap;
    z-index: 9999;
    margin-left: 8px;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.sidebar-history {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sidebar-history-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px 8px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #9ca3af;
}

.sidebar-footer {
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 12px 8px;
    border-top: 1px solid #e5e7eb;
}

.sidebar-footer-btn {
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    transition: all 150ms ease;
}

.sidebar-footer-btn:hover {
    background: #e5e7eb;
    color: #374151;
}

/* 侧边栏折叠状态（仿 Agnes AI） */
.chat-sidebar.collapsed {
    width: 72px;
    overflow: hidden;
}

.chat-sidebar.collapsed .sidebar-header {
    justify-content: center;
    padding: 12px 0;
}

.chat-sidebar.collapsed .sidebar-collapse-btn svg {
    transform: rotate(180deg);
}

.chat-sidebar.collapsed .sidebar-nav-item span,
.chat-sidebar.collapsed .sidebar-logo-text {
    display: none !important;
}

.chat-sidebar.collapsed .sidebar-nav-item {
    justify-content: center;
    padding: 10px 0;
    width: 100%;
}

.chat-sidebar.collapsed .sidebar-nav-item svg {
    width: 22px;
    height: 22px;
}

.chat-sidebar.collapsed .sidebar-history {
    display: none !important;
}

.chat-sidebar.collapsed .sidebar-footer {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    padding: 12px 4px;
    border-top: 1px solid #e5e7eb;
}

.chat-sidebar.collapsed .sidebar-footer-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    margin: 0;
}

.chat-sidebar.collapsed .sidebar-footer-btn svg {
    width: 18px;
    height: 18px;
}

.chat-new-btn {
    width: 100%;
    padding: 10px 16px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 150ms ease;
}

.chat-new-btn:hover {
    background: #eef2ff;
    border-color: #818cf8;
    color: #4f46e5;
}

.chat-new-btn svg {
    width: 16px;
    height: 16px;
}

.chat-history-header {
    padding: 14px 16px 8px;
}

.chat-history-title {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.chat-history-list {
    flex: 1;
    overflow-y: auto;
    padding: 4px 8px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.chat-history-list::-webkit-scrollbar {
    display: none;
}

.chat-history-item {
    padding: 10px 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 150ms ease;
    margin-bottom: 2px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-history-item:hover {
    background: #f3f4f6;
}

.chat-history-item.active {
    background: #eef2ff;
    color: #4f46e5;
}

.chat-history-item-more {
    opacity: 0;
    width: 28px;
    height: 28px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 150ms ease;
    flex-shrink: 0;
    font-size: 18px;
}

.chat-history-item:hover .chat-history-item-more {
    opacity: 1;
}

.chat-history-item-more:hover {
    background: #e5e7eb;
    color: #374151;
}

.chat-history-item-title {
    font-size: 0.8125rem;
    color: inherit;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-history-item-time {
    font-size: 0.6875rem;
    color: #94a3b8;
    margin-top: 2px;
}

.chat-history-empty {
    padding: 20px 16px;
    text-align: center;
    color: #94a3b8;
    font-size: 0.8125rem;
}

/* 右侧聊天区域（圆角+阴影区分） */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
    margin: 12px;
    position: relative;
    z-index: 1;
}

/* 聊天区顶部半透明遮罩 */
.chat-scroll-fade {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 80px;
    background: linear-gradient(to bottom, rgba(243,244,246,1) 0%, rgba(243,244,246,0.8) 40%, rgba(243,244,246,0) 100%);
    pointer-events: none;
    z-index: 5;
    opacity: 0;
    transition: opacity 200ms ease;
    border-radius: 24px 24px 0 0;
}

.chat-scroll-fade.show {
    opacity: 1;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px 0;
    scroll-behavior: smooth;
    overscroll-behavior: contain;
}

.chat-message {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
    padding: 0 40px;
    animation: fadeIn 0.3s ease;
}

.chat-message--user {
    justify-content: flex-end;
    padding: 0 16px;
    box-sizing: border-box;
}

.chat-message--ai {
    padding: 0 16px;
    box-sizing: border-box;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-avatar {
    display: none;
}

.chat-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 0.9375rem;
    line-height: 1.6;
    word-break: break-word;
}

.chat-message--user .chat-bubble {
    max-width: 60%;
    background: #f3f4f6;
    color: #111827;
    border-bottom-right-radius: 4px;
    text-align: right;
    margin-left: auto;
}

.chat-message--ai .chat-bubble {
    max-width: 95%;
    background: transparent;
    color: #111827;
    border-bottom-left-radius: 4px;
    padding: 0 16px;
}

.chat-bubble p { margin: 0 0 8px 0; }
.chat-bubble p:last-child { margin-bottom: 0; }

.chat-bubble pre {
    background: #1e293b;
    color: #e2e8f0;
    padding: 12px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 0.8125rem;
    margin: 8px 0;
}

.chat-bubble code {
    background: rgba(0,0,0,0.06);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875rem;
}

.chat-bubble pre code {
    background: none;
    padding: 0;
}

/* 代码块容器 */
.chat-code-wrap {
    position: relative;
    margin: 8px 0;
    border-radius: 8px;
    overflow: hidden;
    background: #1e293b;
}

.chat-code-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 12px;
    background: #0f172a;
    font-size: 0.6875rem;
    color: #94a3b8;
}

.chat-code-copy {
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

.chat-code-copy:hover {
    background: #334155;
    color: #e2e8f0;
    border-color: #64748b;
}

.chat-code-copy.copied {
    background: #166534;
    color: #fff;
    border-color: #166534;
}

.chat-code-wrap pre {
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

.chat-code-wrap.expanded pre {
    max-height: none;
    overflow: auto;
}

.chat-code-toggle {
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

.chat-code-toggle:hover {
    color: #e2e8f0;
    background: #1e293b;
}

.chat-code-wrap code {
    background: none;
    padding: 0;
    font-size: 0.8125rem;
}

/* 欢迎界面（Agnes AI 风格） */
.chat-welcome {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}

.chat-welcome-title {
    font-size: 2.2rem;
    font-weight: 300;
    color: #1f2937;
    margin: 0;
    text-align: center;
    letter-spacing: -0.02em;
    line-height: 1.3;
    font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.chat-welcome-gradient {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 500;
}

/* 欢迎语下方快捷标签 */
.chat-welcome-tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 24px;
}

/* 快捷标签（输入框下方，聊天时显示） */
.chat-quick-tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 16px;
    margin-bottom: 8px;
}

.chat-tag {
    padding: 8px 16px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: all 150ms ease;
    white-space: nowrap;
}

.chat-tag:hover {
    background: #eef2ff;
    border-color: #818cf8;
    color: #4f46e5;
}

.chat-tag-more {
    background: transparent;
    border: 1px dashed #d1d5db;
    color: #6b7280;
}

/* 输入区域（Agnes AI 风格 - 居中） */
.chat-input-area {
    padding: 16px 40px 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.chat-input-container {
    width: 100%;
    max-width: 900px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    overflow: visible;
    transition: all 200ms ease;
}

.chat-input-container:focus-within {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
}

.chat-input-row {
    display: flex;
    align-items: flex-end;
    padding: 12px 16px;
    gap: 8px;
}

.chat-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.9375rem;
    resize: none;
    min-height: 24px;
    max-height: 120px;
    padding: 4px 0;
    font-family: inherit;
    line-height: 1.5;
}

.chat-input::placeholder {
    color: #9ca3af;
}

.chat-send-btn {
    width: 36px;
    height: 36px;
    background: #4f46e5;
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 150ms ease;
    flex-shrink: 0;
}

.chat-send-btn:hover:not(:disabled) {
    background: #4f46e5;
    color: #fff;
}

.chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.chat-send-btn svg {
    width: 18px;
    height: 18px;
}

/* 输入区域下方操作按钮 */
.chat-input-actions {
    display: flex;
    align-items: center;
    padding: 4px 16px 8px;
    gap: 8px;
}

.chat-action-btn {
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    transition: all 150ms ease;
}

.chat-action-btn:hover {
    background: #f3f4f6;
    color: #6b7280;
}

.chat-action-btn svg {
    width: 20px;
    height: 20px;
}

.chat-input-footer {
    margin-top: 8px;
    text-align: center;
}

.chat-input-hint {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* 加载动画 */
.chat-typing {
    display: flex;
    gap: 4px;
    padding: 8px 0;
}

.chat-typing span {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.chat-typing span:nth-child(2) { animation-delay: 0.2s; }
.chat-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-6px); }
}

/* 滚动按钮（输入框正上方居中） */
.chat-scroll-btn {
    position: absolute;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 32px;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    z-index: 10;
    opacity: 0;
    transition: all 200ms ease;
    pointer-events: none;
}

.chat-scroll-btn.show {
    opacity: 1;
    pointer-events: auto;
}

.chat-scroll-btn:hover {
    border-color: #a78bfa;
    box-shadow: 0 2px 10px rgba(167,139,250,0.2);
}

.chat-scroll-btn svg {
    width: 14px;
    height: 14px;
    color: #1f2937;
    stroke-width: 3;
}

/* 默认箭头朝下（滚到底部） */
.chat-scroll-btn svg.arrow-up { display: none; }
.chat-scroll-btn svg.arrow-down { display: block; }

/* 在底部时箭头朝上（滚到顶部） */
.chat-scroll-btn.at-bottom svg.arrow-up { display: block; }
.chat-scroll-btn.at-bottom svg.arrow-down { display: none; }

/* 三点菜单 */
.chat-context-menu {
    position: fixed;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    padding: 6px 0;
    z-index: 1000;
    min-width: 160px;
    display: none;
}

.chat-context-menu.show {
    display: block;
}

.chat-context-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: background 100ms ease;
}

.chat-context-item:hover {
    background: #f3f4f6;
}

.chat-context-item--danger {
    color: #ef4444;
}

.chat-context-item--danger:hover {
    background: #fef2f2;
}

.chat-context-item svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

/* 移动端适配 */
/* 手机端使用悬浮聊天框，此页面仅桌面端显示 */
</style>

<section class="chat-layout">
    <!-- 左侧边栏（严格按 Agnes AI 设计） -->
    <aside class="chat-sidebar" id="chatSidebar">
        <!-- 折叠按钮 -->
        <div class="sidebar-header">
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="折叠侧边栏">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
            </button>
        </div>

        <!-- 新任务按钮 -->
        <div class="sidebar-nav">
            <button class="sidebar-nav-item sidebar-nav-item--active" id="chatNewBtn" title="新任务">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <span>新任务</span>
            </button>
            <button class="sidebar-nav-item" title="搜索">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>搜索</span>
            </button>
            <button class="sidebar-nav-item" title="定时任务">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>定时任务</span>
            </button>
            <button class="sidebar-nav-item" title="库">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                </svg>
                <span>库</span>
            </button>
        </div>

        <!-- 对话历史 -->
        <div class="sidebar-history">
            <div class="sidebar-history-header">
                <span>所有任务</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
            <div class="chat-history-list" id="chatHistoryList">
                <div class="chat-history-empty">暂无任务记录</div>
            </div>
        </div>

        <!-- 底部图标 -->
        <div class="sidebar-footer">
            <button class="sidebar-footer-btn" title="工具箱">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </button>
            <button class="sidebar-footer-btn" title="定时任务">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </button>
            <button class="sidebar-footer-btn" title="设置">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
            </button>
            <button class="sidebar-footer-btn" title="更多应用">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
            </button>
        </div>
    </aside>

    <!-- 右侧聊天区域 -->
    <main class="chat-main">
        <!-- 顶部半透明遮罩 -->
        <div class="chat-scroll-fade" id="scrollFade"></div>
        <div class="chat-messages" id="chatMessages">
            <!-- 欢迎界面 -->
            <div class="chat-welcome" id="chatWelcome">
                <h2 class="chat-welcome-title">
                    <span class="chat-welcome-gradient">欢迎</span>
                    我能为您做什么？
                </h2>
            </div>
        </div>

        <!-- 欢迎语下方快捷标签 -->
        <div class="chat-welcome-tags" id="chatWelcomeTags">
            <button class="chat-tag" data-prompt="帮我制作一个 AI 幻灯片">AI 幻灯片</button>
            <button class="chat-tag" data-prompt="帮我创建一个网站">创建网站</button>
            <button class="chat-tag" data-prompt="帮我做 AI 设计">AI 设计</button>
            <button class="chat-tag" data-prompt="帮我做 AI 表格">AI 表格</button>
            <button class="chat-tag chat-tag-more">更多</button>
        </div>

    <!-- 滚动按钮（双向：上/下） -->
    <button class="chat-scroll-btn" id="scrollBtn">
        <!-- 箭头朝上（滚到顶部） -->
        <svg class="arrow-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="19" x2="12" y2="5"/>
            <polyline points="5 12 12 5 19 12"/>
        </svg>
        <!-- 箭头朝下（滚到底部） -->
        <svg class="arrow-down" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <polyline points="19 12 12 19 5 12"/>
        </svg>
    </button>
        <!-- 输入区域（居中） -->
        <div class="chat-input-area">
            <div class="chat-input-container">
                <div class="chat-input-row">
                    <textarea class="chat-input" id="chatInput" placeholder="分配一个任务或提问任何问题" rows="1"></textarea>
                    <button class="chat-send-btn" id="chatSendBtn" disabled>
                        <!-- 发送图标 -->
                        <svg class="icon-send" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        <!-- 停止图标（默认隐藏） -->
                        <svg class="icon-stop" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                            <rect x="6" y="6" width="12" height="12" rx="2"/>
                        </svg>
                    </button>
                </div>
                <div class="chat-input-actions">
                    <button class="chat-action-btn" id="chatPlusBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </button>
                    <button class="chat-action-btn" id="chatAttachBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="chat-input-footer">
                <span class="chat-input-hint">基于 Agnes AI · 内容仅供参考</span>
            </div>
        </div>
    </main>



    <!-- 右键菜单 -->
    <div class="chat-context-menu" id="chatContextMenu">
        <div class="chat-context-item" data-action="share">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            分享
        </div>
        <div class="chat-context-item" data-action="rename">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            重命名
        </div>
        <div class="chat-context-item" data-action="favorite">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            添加到收藏夹
        </div>
        <div class="chat-context-item chat-context-item--danger" data-action="delete">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            删除
        </div>
    </div>
</section>

<script>
(function() {
    'use strict';

    var chatMessages = document.getElementById('chatMessages');
    var chatInput = document.getElementById('chatInput');
    var chatSendBtn = document.getElementById('chatSendBtn');
    var chatWelcome = document.getElementById('chatWelcome');
    var chatNewBtn = document.getElementById('chatNewBtn');
    var chatHistoryList = document.getElementById('chatHistoryList');
    var STORAGE_KEY = 'aiphoto_chats';
    var currentChatId = null;
    var conversationHistory = [];
    var isGenerating = false;
    var contextMenu = null;
    var contextTarget = null;

    function setLoading(on) {
        isGenerating = on;
        var iconSend = chatSendBtn.querySelector('.icon-send');
        var iconStop = chatSendBtn.querySelector('.icon-stop');
        if (on) {
            if (iconSend) iconSend.style.display = 'none';
            if (iconStop) iconStop.style.display = 'block';
            chatSendBtn.disabled = false;
        } else {
            if (iconSend) iconSend.style.display = 'block';
            if (iconStop) iconStop.style.display = 'none';
            chatSendBtn.disabled = chatInput.value.trim().length === 0;
            // 移除所有加载动画
            document.querySelectorAll('.chat-typing').forEach(function(el) {
                el.remove();
            });
        }
    }

    // 初始化
    init();

    function init() {
        loadChatList();
        // 检查是否有保存的当前对话
        var lastChatId = localStorage.getItem('aiphoto_current_chat');
        if (lastChatId) {
            var chats = getAllChats();
            if (chats[lastChatId]) {
                loadChat(lastChatId);
            } else {
                createNewChat();
            }
        } else {
            // 没有保存的对话，显示欢迎界面
            chatMessages.innerHTML = getWelcomeHTML();
            document.getElementById('chatWelcomeTags').style.display = 'flex';
            bindWelcomeEvents();
        }
        bindEvents();
        
        // 初始化完成后显示布局
        document.querySelector('.chat-layout').classList.add('ready');
    }

    function bindEvents() {
        // 新对话按钮
        chatNewBtn.addEventListener('click', createNewChat);

        // 输入框
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            chatSendBtn.disabled = this.value.trim().length === 0 || isGenerating;
        });

        // 发送
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!chatSendBtn.disabled) sendMessage();
            }
        });

        // 发送/停止按钮切换
        chatSendBtn.addEventListener('click', function() {
            if (isGenerating) {
                // 停止生成
                if (genAbortController) genAbortController.abort();
                setLoading(false);
            } else {
                sendMessage();
            }
        });

        // 快捷建议（旧版兼容）
        document.querySelectorAll('.chat-suggestion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                chatInput.value = this.getAttribute('data-prompt');
                chatInput.dispatchEvent(new Event('input'));
                sendMessage();
            });
        });

        // 快捷标签
        document.querySelectorAll('.chat-tag[data-prompt]').forEach(function(tag) {
            tag.addEventListener('click', function() {
                chatInput.value = this.getAttribute('data-prompt');
                chatInput.dispatchEvent(new Event('input'));
                sendMessage();
            });
        });

        // 滚动按钮 + 顶部遮罩
        var scrollBtn = document.getElementById('scrollBtn');
        var scrollFade = document.getElementById('scrollFade');
        if (scrollBtn || scrollFade) {
            chatMessages.addEventListener('scroll', function() {
                var maxScroll = chatMessages.scrollHeight - chatMessages.clientHeight;

                // 滚动按钮逻辑
                if (scrollBtn) {
                    if (maxScroll > 100) {
                        scrollBtn.classList.add('show');
                        if (chatMessages.scrollTop >= maxScroll - 50) {
                            scrollBtn.classList.add('at-bottom');
                        } else {
                            scrollBtn.classList.remove('at-bottom');
                        }
                    } else {
                        scrollBtn.classList.remove('show');
                    }
                }

                // 顶部遮罩逻辑
                if (scrollFade) {
                    if (chatMessages.scrollTop > 50) {
                        scrollFade.classList.add('show');
                    } else {
                        scrollFade.classList.remove('show');
                    }
                }
            });

            if (scrollBtn) {
                scrollBtn.addEventListener('click', function() {
                    var maxScroll = chatMessages.scrollHeight - chatMessages.clientHeight;
                    if (chatMessages.scrollTop >= maxScroll - 50) {
                        chatMessages.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        chatMessages.scrollTo({ top: maxScroll, behavior: 'smooth' });
                    }
                });
            }
        }

        // 侧边栏折叠
        var collapseBtn = document.getElementById('sidebarCollapseBtn');
        var sidebar = document.getElementById('chatSidebar');
        if (collapseBtn && sidebar) {
            collapseBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }

        // 三点菜单
        contextMenu = document.getElementById('chatContextMenu');

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.chat-context-menu') && !e.target.closest('.chat-history-item-more')) {
                contextMenu.classList.remove('show');
                contextTarget = null;
            }
        });

        document.querySelectorAll('.chat-context-item').forEach(function(menuItem) {
            menuItem.addEventListener('click', function() {
                var action = this.getAttribute('data-action');
                if (!contextTarget) return;
                if (action === 'delete') {
                    deleteChat(contextTarget);
                } else if (action === 'rename') {
                    var newName = prompt('请输入新名称：');
                    if (newName) renameChat(contextTarget, newName);
                }
                contextMenu.classList.remove('show');
                contextTarget = null;
            });
        });
    }

    // ========== 对话管理 ==========

    function getAllChats() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch (e) {
            return {};
        }
    }

    function saveAllChats(chats) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(chats));
    }

    function createNewChat() {
        // 如果已经在欢迎页（无当前对话），不做任何操作
        if (!currentChatId) {
            // 检查是否已经在欢迎页
            var welcome = document.getElementById('chatWelcome');
            if (welcome) return; // 已经在欢迎页，不重复创建
        }

        // 保存当前对话（如果有消息）
        if (currentChatId) {
            saveCurrentChat();
        }

        // 切换到新对话状态
        currentChatId = null;
        localStorage.removeItem('aiphoto_current_chat');
        conversationHistory = [];

        // 显示欢迎页
        chatMessages.innerHTML = getWelcomeHTML();
        document.getElementById('chatWelcomeTags').style.display = 'flex';
        bindWelcomeEvents();
        loadChatList();
    }

    function loadChat(chatId) {
        var chats = getAllChats();
        var chat = chats[chatId];
        if (!chat) return;

        currentChatId = chatId;
        conversationHistory = chat.history || [];
        localStorage.setItem('aiphoto_current_chat', chatId);

        // 清空消息区域
        chatMessages.innerHTML = '';

        if (chat.messages && chat.messages.length > 0) {
            // 显示历史消息（使用保存的 HTML）
            chat.messages.forEach(function(msg) {
                addMessageToDOM(msg.html, msg.type, false, true);
            });
            scrollToBottom();
        } else {
            // 显示欢迎界面
            chatMessages.innerHTML = getWelcomeHTML();
            bindWelcomeEvents();
        }

        // 加载已有对话时，确保欢迎标签隐藏（关键修复：避免标签混入聊天记录）
        var welcomeTags = document.getElementById('chatWelcomeTags');
        if (welcomeTags) welcomeTags.style.display = 'none';

        // 更新侧边栏选中状态
        chatHistoryList.querySelectorAll('.chat-history-item').forEach(function(item) {
            item.classList.toggle('active', item.dataset.chatId === chatId);
        });
    }

    function saveCurrentChat() {
        if (!currentChatId) return;
        var chats = getAllChats();
        if (!chats[currentChatId]) return;

        // 收集所有消息（排除欢迎界面和加载动画）
        var messages = [];
        var messageElements = chatMessages.querySelectorAll('.chat-message');
        messageElements.forEach(function(msg) {
            // 跳过加载动画
            if (msg.querySelector('.chat-typing')) return;
            
            var type = msg.classList.contains('chat-message--user') ? 'user' : 'ai';
            var bubble = msg.querySelector('.chat-bubble');
            if (bubble) {
                messages.push({
                    html: bubble.innerHTML,
                    plain: bubble.textContent || bubble.innerText,
                    type: type
                });
            }
        });

        chats[currentChatId].messages = messages;
        chats[currentChatId].history = conversationHistory.slice(-20);
        chats[currentChatId].updated = Date.now();

        // 自动设置标题（取第一条用户消息）
        if (messages.length > 0 && chats[currentChatId].title === '新对话') {
            var firstUserMsg = messages.find(function(m) { return m.type === 'user'; });
            if (firstUserMsg) {
                chats[currentChatId].title = firstUserMsg.plain.substring(0, 30) + (firstUserMsg.plain.length > 30 ? '...' : '');
            }
        }

        saveAllChats(chats);
        loadChatList();
    }

    function deleteChat(chatId) {
        var chats = getAllChats();
        delete chats[chatId];
        saveAllChats(chats);

        if (currentChatId === chatId) {
            currentChatId = null;
            localStorage.removeItem('aiphoto_current_chat');
            createNewChat();
        } else {
            loadChatList();
        }
    }

    function loadChatList() {
        var chats = getAllChats();
        var sorted = Object.values(chats).sort(function(a, b) { return b.updated - a.updated; });

        if (sorted.length === 0) {
            chatHistoryList.innerHTML = '<div class="chat-history-empty">暂无对话记录</div>';
            return;
        }

        var html = '';
        sorted.forEach(function(chat) {
            var active = chat.id === currentChatId ? ' active' : '';
            html += '<div class="chat-history-item' + active + '" data-chat-id="' + chat.id + '">' +
                    '<div class="chat-history-item-title">' + escapeHtml(chat.title) + '</div>' +
                    '<button class="chat-history-item-more" data-chat-id="' + chat.id + '">' +
                    '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>' +
                    '</button></div>';
        });

        chatHistoryList.innerHTML = html;

        // 绑定点击事件
        chatHistoryList.querySelectorAll('.chat-history-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                if (e.target.closest('.chat-history-item-more')) return;
                loadChat(this.dataset.chatId);
            });
        });

        // 绑定三点按钮点击事件
        chatHistoryList.querySelectorAll('.chat-history-item-more').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var chatId = this.dataset.chatId;
                var rect = this.getBoundingClientRect();
                contextMenu.style.left = rect.right + 'px';
                contextMenu.style.top = rect.top + 'px';
                contextTarget = chatId;
                contextMenu.classList.add('show');
            });
        });
    }

    // ========== 消息处理 ==========

    var genAbortController = null;

    function sendMessage() {
        var message = chatInput.value.trim();
        if (!message || isGenerating) return;

        // 如果没有当前对话，创建一个新对话
        if (!currentChatId) {
            var chatId = 'chat_' + Date.now();
            var chats = getAllChats();
            chats[chatId] = {
                id: chatId,
                title: '新对话',
                messages: [],
                history: [],
                created: Date.now(),
                updated: Date.now()
            };
            saveAllChats(chats);
            currentChatId = chatId;
            localStorage.setItem('aiphoto_current_chat', chatId);
        }

        // 隐藏欢迎界面
        var welcome = document.getElementById('chatWelcome');
        if (welcome) {
            welcome.remove();
        }

        // 添加用户消息
        addMessageToDOM(message, 'user', true);
        conversationHistory.push({ role: 'user', content: message });

        // 立即保存用户消息到对话记录
        saveCurrentChat();

        // 清空输入
        chatInput.value = '';
        chatInput.style.height = 'auto';
        chatSendBtn.disabled = true;

        // 显示加载
        setLoading(true);

        // 隐藏欢迎标签
        var welcomeTags = document.getElementById('chatWelcomeTags');
        if (welcomeTags) welcomeTags.style.display = 'none';

        // 创建 AI 消息气泡（先显示加载动画）
        var aiDiv = document.createElement('div');
        aiDiv.className = 'chat-message chat-message--ai';
        aiDiv.innerHTML = '<div class="chat-bubble"><div class="chat-typing"><span></span><span></span><span></span></div></div>';
        chatMessages.appendChild(aiDiv);
        var aiBubble = aiDiv.querySelector('.chat-bubble');
        scrollToBottom();

        var fullReply = '';
        var firstChunk = true;

        // 创建 AbortController 用于停止
        genAbortController = new AbortController();

        var streamUrl = aiphotoAjax.url + '?action=aiphoto_chat_stream&nonce=' +
            encodeURIComponent(aiphotoAjax.nonce) +
            '&message=' + encodeURIComponent(message) +
            '&history=' + encodeURIComponent(JSON.stringify(conversationHistory.slice(-100)));

        fetch(streamUrl, { signal: genAbortController.signal })
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
                                finishChatStream(fullReply);
                                return;
                            }
                            try {
                                var data = JSON.parse(jsonStr);
                                if (data.error) {
                                    aiBubble.innerHTML = formatMessage(data.error);
                                    finishChatStream('');
                                    return;
                                }
                                if (data.text) {
                                    fullReply += data.text;
                                    if (firstChunk) {
                                        firstChunk = false;
                                        aiBubble.innerHTML = '';
                                    }
                                    aiBubble.innerHTML = formatMessage(fullReply);
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
                    // 用户取消
                    setLoading(false);
                    chatSendBtn.disabled = chatInput.value.trim().length === 0;
                    return;
                }
                aiBubble.innerHTML = formatMessage('网络错误，请检查网络连接后重试。');
                finishChatStream('');
            });

        function finishChatStream(reply) {
            setLoading(false);
            chatSendBtn.disabled = chatInput.value.trim().length === 0;
            chatInput.focus();
            if (reply) {
                conversationHistory.push({ role: 'assistant', content: reply });
                saveCurrentChat();
            }
        }
    }

    function addMessageToDOM(text, type, animate, isHtml) {
        var div = document.createElement('div');
        div.className = 'chat-message chat-message--' + type;
        if (!animate) div.style.animation = 'none';

        var content = isHtml ? text : formatMessage(text);

        div.innerHTML = '<div class="chat-bubble">' + content + '</div>';

        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function addLoading() {
        var id = 'loading-' + Date.now();
        var div = document.createElement('div');
        div.className = 'chat-message chat-message--ai';
        div.id = id;
        div.innerHTML = '<div class="chat-bubble"><div class="chat-typing"><span></span><span></span><span></span></div></div>';
        chatMessages.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeLoading(id) {
        var el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // ========== 工具函数 ==========

    function getWelcomeHTML() {
        return '<div class="chat-welcome" id="chatWelcome">' +
            '<h2 class="chat-welcome-title">' +
            '<span class="chat-welcome-gradient">欢迎</span>' +
            ' 我能为您做什么？' +
            '</h2>' +
            '</div>';
    }

    function bindWelcomeEvents() {
        document.querySelectorAll('.chat-suggestion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                chatInput.value = this.getAttribute('data-prompt');
                chatInput.dispatchEvent(new Event('input'));
                sendMessage();
            });
        });
    }

    var pageCodeIdCounter = 0;

    function formatMessage(text) {
        text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        // 1. 处理完整闭合的代码块
        text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, function(match, lang, code) {
            var id = 'chat-code-' + (++pageCodeIdCounter);
            var langLabel = lang || 'code';
            var lines = code.trim().split('\n').length;
            var needToggle = lines > 8;
            return '<div class="chat-code-wrap' + (needToggle ? '' : ' expanded') + '" id="' + id + '">' +
                '<div class="chat-code-header"><span>' + langLabel + '</span>' +
                '<button class="chat-code-copy" data-code="' + id + '">复制</button></div>' +
                '<pre><code>' + code.trim() + '</code></pre>' +
                (needToggle ? '<button class="chat-code-toggle" data-toggle="' + id + '">▼ 查看全部</button>' : '') +
                '</div>';
        });

        // 2. 处理未闭合的代码块
        var pendingCodeMatch = text.match(/```(\w*)\n?([\s\S]*?)$/);
        if (pendingCodeMatch && pendingCodeMatch[2].trim().length > 0) {
            var id = 'chat-code-' + (++pageCodeIdCounter);
            var langLabel = pendingCodeMatch[1] || 'code';
            var codeContent = pendingCodeMatch[2].trim();
            var lines = codeContent.split('\n').length;
            var needToggle = lines > 8;
            text = text.replace(/```(\w*)\n?[\s\S]*$/, '<div class="chat-code-wrap' + (needToggle ? '' : ' expanded') + '" id="' + id + '">' +
                '<div class="chat-code-header"><span>' + langLabel + '</span>' +
                '<button class="chat-code-copy" data-code="' + id + '">复制</button></div>' +
                '<pre><code>' + codeContent + '</code></pre>' +
                (needToggle ? '<button class="chat-code-toggle" data-toggle="' + id + '">▼ 查看全部</button>' : '') +
                '</div>');
        }

        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/^### (.+)$/gm, '<h3>$1</h3>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    // 事件委托：复制 + 展开收起
    chatMessages.addEventListener('click', function(e) {
        // 复制按钮
        var copyBtn = e.target.closest('.chat-code-copy');
        if (copyBtn) {
            e.stopPropagation();
            var codeId = copyBtn.getAttribute('data-code');
            var wrap = document.getElementById(codeId);
            if (!wrap) return;
            var codeEl = wrap.querySelector('code');
            var code = codeEl.innerText || codeEl.textContent;
            var temp = document.createElement('textarea');
            temp.innerHTML = code.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            code = temp.value;
            // 优先用 navigator.clipboard，失败则用 textarea fallback
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
        // 展开/收起按钮
        var toggleBtn = e.target.closest('.chat-code-toggle');
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

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        var date = new Date(timestamp);
        var now = new Date();
        var diff = now - date;

        if (diff < 60000) return '刚刚';
        if (diff < 3600000) return Math.floor(diff / 60000) + '分钟前';
        if (diff < 86400000) return Math.floor(diff / 3600000) + '小时前';
        if (diff < 604800000) return Math.floor(diff / 86400000) + '天前';

        return date.getMonth() + 1 + '月' + date.getDate() + '日';
    }

    // 聊天区域滚动拦截通过 CSS overscroll-behavior 实现（无迟滞）
})();
</script>

<?php get_footer(); ?>
