<?php
/**
 * Template Name: AI 聊天
 * Description: AIPhoto AI 智能聊天
 */

get_header();

$settings = aiphoto_get_settings();
?>

<style>
/* 初始隐藏，避免闪烁 */
.chat-layout {
    visibility: hidden;
}

.chat-layout.ready {
    visibility: visible;
}

.chat-layout {
    display: flex;
    height: calc(100vh - 100px);
    margin-top: 90px;
    background: #fff;
    position: relative;
}

/* 左侧边栏 */
.chat-sidebar {
    width: 260px;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.04);
}

.chat-sidebar-header {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.chat-new-btn {
    width: 100%;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #0f172a;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 150ms ease;
}

.chat-new-btn:hover {
    border-color: #7c3aed;
    color: #7c3aed;
    background: #f5f3ff;
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
}

.chat-history-item {
    padding: 10px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 150ms ease;
    margin-bottom: 2px;
}

.chat-history-item:hover {
    background: #e2e8f0;
}

.chat-history-item.active {
    background: #e0e7ff;
    color: #7c3aed;
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

/* 右侧聊天区域 */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
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
    margin-bottom: 24px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
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
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-avatar {
    display: none;
}

.chat-message--user .chat-avatar {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
}

.chat-message--ai .chat-avatar {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: #fff;
}

.chat-bubble {
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.9375rem;
    line-height: 1.7;
    word-break: break-word;
}

.chat-message--user .chat-bubble {
    max-width: 500px;
    background: transparent;
    color: #0f172a;
    border-bottom-right-radius: 4px;
    text-align: right;
}

.chat-message--ai .chat-bubble {
    max-width: 800px;
    background: transparent;
    color: #0f172a;
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

/* 欢迎界面 */
.chat-welcome {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.chat-welcome-logo {
    width: 80px;
    height: 80px;
    margin-bottom: 24px;
    background: linear-gradient(135deg, #7c3aed, #f97316);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-welcome-logo svg {
    width: 40px;
    height: 40px;
    stroke: #fff;
}

.chat-welcome h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
}

.chat-welcome p {
    color: #64748b;
    font-size: 0.9375rem;
    margin-bottom: 32px;
}

.chat-suggestions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    max-width: 500px;
    width: 100%;
}

.chat-suggestion {
    padding: 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    text-align: left;
    cursor: pointer;
    transition: all 150ms ease;
}

.chat-suggestion:hover {
    border-color: #7c3aed;
    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1);
}

.chat-suggestion-icon {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.chat-suggestion-icon svg {
    width: 18px;
    height: 18px;
    stroke: #64748b;
}

.chat-suggestion-text {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.4;
}

/* 输入区域 */
.chat-input-area {
    padding: 16px 0 24px;
    border-top: 1px solid #f1f5f9;
    background: #fff;
}

.chat-input-container {
    max-width: 800px;
    margin: 0 auto;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    transition: all 200ms ease;
}

.chat-input-container:focus-within {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.chat-input-row {
    display: flex;
    align-items: flex-end;
    padding: 8px 12px;
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
    padding: 8px 4px;
    font-family: inherit;
    line-height: 1.5;
}

.chat-input::placeholder {
    color: #94a3b8;
}

.chat-send-btn {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 150ms ease;
    flex-shrink: 0;
}

.chat-send-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
}

.chat-send-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

.chat-send-btn svg {
    width: 18px;
    height: 18px;
}

.chat-input-footer {
    padding: 8px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #f1f5f9;
}

.chat-input-hint {
    font-size: 0.75rem;
    color: #94a3b8;
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

/* 移动端适配 */
/* 手机端使用悬浮聊天框，此页面仅桌面端显示 */
</style>

<section class="chat-layout">
    <!-- 左侧边栏 -->
    <aside class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <button class="chat-new-btn" id="chatNewBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                新对话
            </button>
        </div>
        <div class="chat-history-header">
            <span class="chat-history-title">最近对话</span>
        </div>
        <div class="chat-history-list" id="chatHistoryList">
            <div class="chat-history-empty">暂无对话记录</div>
        </div>
    </aside>

    <!-- 右侧聊天区域 -->
    <main class="chat-main">
        <div class="chat-messages" id="chatMessages">
            <!-- 欢迎界面 -->
            <div class="chat-welcome" id="chatWelcome">
                <div class="chat-welcome-logo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                </div>
                <h2>你好，有什么想聊的？</h2>
                <p>我可以帮你写文案、回答问题、提供创作灵感</p>
                <div class="chat-suggestions">
                    <button class="chat-suggestion" data-prompt="帮我写一段关于海边日落的描述">
                        <div class="chat-suggestion-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </div>
                        <div class="chat-suggestion-text">写一段海边日落描述</div>
                    </button>
                    <button class="chat-suggestion" data-prompt="解释一下什么是 AI 绘画">
                        <div class="chat-suggestion-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <div class="chat-suggestion-text">什么是 AI 绘画</div>
                    </button>
                    <button class="chat-suggestion" data-prompt="给我一些图片创作的灵感">
                        <div class="chat-suggestion-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <div class="chat-suggestion-text">给我创作灵感</div>
                    </button>
                    <button class="chat-suggestion" data-prompt="帮我优化这个提示词：一只可爱的猫咪">
                        <div class="chat-suggestion-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                        <div class="chat-suggestion-text">优化提示词</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- 输入区域 -->
        <div class="chat-input-area">
            <div class="chat-input-container">
                <div class="chat-input-row">
                    <textarea class="chat-input" id="chatInput" placeholder="向 Aiphoto 提问..." rows="1"></textarea>
                    <button class="chat-send-btn" id="chatSendBtn" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>
                </div>
                <div class="chat-input-footer">
                    <span class="chat-input-hint">基于 Agnes AI · 内容仅供参考</span>
                </div>
            </div>
        </div>
    </main>
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

        chatSendBtn.addEventListener('click', sendMessage);

        // 快捷建议
        document.querySelectorAll('.chat-suggestion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                chatInput.value = this.getAttribute('data-prompt');
                chatInput.dispatchEvent(new Event('input'));
                sendMessage();
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
        loadChat(chatId);
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
            var time = formatTime(chat.updated);
            var active = chat.id === currentChatId ? ' active' : '';
            html += '<div class="chat-history-item' + active + '" data-chat-id="' + chat.id + '">' +
                    '<div class="chat-history-item-title">' + escapeHtml(chat.title) + '</div>' +
                    '<div class="chat-history-item-time">' + time + '</div>' +
                    '</div>';
        });

        chatHistoryList.innerHTML = html;

        // 绑定点击事件
        chatHistoryList.querySelectorAll('.chat-history-item').forEach(function(item) {
            item.addEventListener('click', function() {
                loadChat(this.dataset.chatId);
            });
            item.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm('确定要删除这个对话吗？')) {
                    deleteChat(this.dataset.chatId);
                }
            });
        });
    }

    // ========== 消息处理 ==========

    function sendMessage() {
        var message = chatInput.value.trim();
        if (!message || isGenerating) return;

        // 如果没有当前对话，创建一个
        if (!currentChatId) {
            createNewChat();
        }

        // 隐藏欢迎界面
        var welcome = document.getElementById('chatWelcome');
        if (welcome) {
            welcome.remove();
        }

        // 添加用户消息
        addMessageToDOM(message, 'user', true);
        conversationHistory.push({ role: 'user', content: message });

        // 清空输入
        chatInput.value = '';
        chatInput.style.height = 'auto';
        chatSendBtn.disabled = true;

        // 显示加载
        isGenerating = true;

        // 创建 AI 消息气泡（先显示加载动画）
        var aiDiv = document.createElement('div');
        aiDiv.className = 'chat-message chat-message--ai';
        aiDiv.innerHTML = '<div class="chat-bubble"><div class="chat-typing"><span></span><span></span><span></span></div></div>';
        chatMessages.appendChild(aiDiv);
        var aiBubble = aiDiv.querySelector('.chat-bubble');
        scrollToBottom();

        var fullReply = '';
        var firstChunk = true;

        var streamUrl = aiphotoAjax.url + '?action=aiphoto_chat_stream&nonce=' +
            encodeURIComponent(aiphotoAjax.nonce) +
            '&message=' + encodeURIComponent(message) +
            '&history=' + encodeURIComponent(JSON.stringify(conversationHistory.slice(-10)));

        fetch(streamUrl)
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
                aiBubble.innerHTML = formatMessage('网络错误，请检查网络连接后重试。');
                finishChatStream('');
            });

        function finishChatStream(reply) {
            isGenerating = false;
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
            '<div class="chat-welcome-logo">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>' +
            '</div>' +
            '<h2>你好，有什么想聊的？</h2>' +
            '<p>我可以帮你写文案、回答问题、提供创作灵感</p>' +
            '<div class="chat-suggestions">' +
            '<button class="chat-suggestion" data-prompt="帮我写一段关于海边日落的描述"><div class="chat-suggestion-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><div class="chat-suggestion-text">写一段海边日落描述</div></button>' +
            '<button class="chat-suggestion" data-prompt="解释一下什么是 AI 绘画"><div class="chat-suggestion-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div><div class="chat-suggestion-text">什么是 AI 绘画</div></button>' +
            '<button class="chat-suggestion" data-prompt="给我一些图片创作的灵感"><div class="chat-suggestion-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><div class="chat-suggestion-text">给我创作灵感</div></button>' +
            '<button class="chat-suggestion" data-prompt="帮我优化这个提示词：一只可爱的猫咪"><div class="chat-suggestion-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg></div><div class="chat-suggestion-text">优化提示词</div></button>' +
            '</div></div>';
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
