/**
 * AIPhoto - Agnes 风格主 JavaScript
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initGeneratorFlow();
        initSettingsPanel();
        initUploadArea();
        initTextareaAutoResize();
        initSendButton();
        initDiscoverTabs();
        initThemeToggle();
        initRecentWorks();
    });

    // ==================== 核心状态 ====================
    var state = {
        hasInput: false,
        isGenerating: false,
        selectedRatio: 'auto',
        selectedResolution: '1K',
        uploadedImages: [],
        currentTemplate: ''
    };

    // ==================== 生成器流程 ====================
    function initGeneratorFlow() {
        var promptInput = document.getElementById('agnesPrompt');
        var welcomeEl = document.getElementById('agnesWelcome');
        var cardEl = document.getElementById('agnesCard');
        var contentEl = document.getElementById('agnesContent');
        var recSection = document.getElementById('recommendedSection');
        var discoverTabs = document.getElementById('discoverTabs');
        var newWorkBtn = document.getElementById('newWorkBtn');

        // 监听输入变化
        promptInput.addEventListener('input', function() {
            var hasText = this.value.trim().length > 0;
            var hasImages = state.uploadedImages.length > 0;

            if (hasText || hasImages) {
                if (!state.hasInput) {
                    // 首次输入：隐藏欢迎，显示卡片
                    welcomeEl.style.display = 'none';
                    cardEl.classList.add('visible');
                    if (discoverTabs) discoverTabs.style.display = 'flex';
                    if (recSection) recSection.style.display = 'block';
                    if (newWorkBtn) newWorkBtn.style.display = 'inline-flex';
                    state.hasInput = true;
                }
            } else {
                if (state.hasInput && state.uploadedImages.length === 0) {
                    // 清空输入：恢复欢迎态
                    welcomeEl.style.display = '';
                    cardEl.classList.remove('visible');
                    if (discoverTabs) discoverTabs.style.display = 'none';
                    if (recSection) recSection.style.display = 'none';
                    if (newWorkBtn) newWorkBtn.style.display = 'none';
                    state.hasInput = false;
                }
            }

            // 更新发送按钮状态
            updateSendButton();
        });

        // 新作品按钮
        if (newWorkBtn) {
            newWorkBtn.addEventListener('click', function() {
                promptInput.value = '';
                state.uploadedImages = [];
                renderUploadPreviews();
                promptInput.dispatchEvent(new Event('input'));
                promptInput.focus();
            });
        }
    }

    // ==================== 设置面板 ====================
    function initSettingsPanel() {
        var settingsBtn = document.getElementById('settingsBtn');
        var overlay = document.getElementById('settingsOverlay');
        var closeBtn = document.getElementById('settingsClose');
        var ratioGrid = document.getElementById('ratioGrid');
        var resGroup = document.getElementById('resolutionGroup');

        // 打开设置
        if (settingsBtn) {
            settingsBtn.addEventListener('click', function() {
                overlay.style.display = 'flex';
                // 强制重排后添加 open 类以触发动画
                requestAnimationFrame(function() {
                    overlay.classList.add('open');
                });
            });
        }

        // 关闭设置
        function closeSettings() {
            overlay.classList.remove('open');
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 250);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSettings);
        }

        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeSettings();
            });
        }

        // ESC 关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('open')) {
                closeSettings();
            }
        });

        // 比例选择
        if (ratioGrid) {
            ratioGrid.addEventListener('click', function(e) {
                var btn = e.target.closest('.ratio-btn');
                if (!btn) return;

                ratioGrid.querySelectorAll('.ratio-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
                state.selectedRatio = btn.getAttribute('data-ratio');
            });
        }

        // 分辨率选择
        if (resGroup) {
            resGroup.addEventListener('click', function(e) {
                var btn = e.target.closest('.res-btn');
                if (!btn) return;

                resGroup.querySelectorAll('.res-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
                state.selectedResolution = btn.getAttribute('data-res');
            });
        }
    }

    // ==================== 上传区域 ====================
    function initUploadArea() {
        var uploadBtn = document.getElementById('uploadPlusBtn');
        var fileInput = document.getElementById('fileInput');
        var cardUploadArea = document.getElementById('cardUploadArea');

        if (!uploadBtn || !fileInput) return;

        uploadBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            var files = Array.from(this.files);
            files.forEach(function(file) {
                if (state.uploadedImages.length >= 4) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    state.uploadedImages.push(e.target.result);
                    renderUploadPreviews();
                    // 触发输入事件以显示卡片
                    var promptInput = document.getElementById('agnesPrompt');
                    if (promptInput && !state.hasInput) {
                        promptInput.dispatchEvent(new Event('input'));
                    }
                };
                reader.readAsDataURL(file);
            });
            fileInput.value = '';
        });
    }

    function renderUploadPreviews() {
        var container = document.getElementById('uploadPreviews');
        if (!container) return;

        container.innerHTML = '';
        state.uploadedImages.forEach(function(src, idx) {
            var div = document.createElement('div');
            div.className = 'upload-preview-item';
            div.innerHTML = '<img src="' + src + '" alt="参考图">' +
                '<button type="button" class="upload-preview-remove" data-idx="' + idx + '">✕</button>';
            container.appendChild(div);
        });

        // 绑定删除
        container.querySelectorAll('.upload-preview-remove').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var idx = parseInt(this.getAttribute('data-idx'));
                state.uploadedImages.splice(idx, 1);
                renderUploadPreviews();
                // 触发输入事件
                var promptInput = document.getElementById('agnesPrompt');
                if (promptInput) {
                    promptInput.dispatchEvent(new Event('input'));
                }
            });
        });
    }

    // ==================== 文本框自适应高度 ====================
    function initTextareaAutoResize() {
        var textarea = document.getElementById('agnesPrompt');
        if (!textarea) return;

        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });

        // Enter 发送（Shift+Enter 换行）
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                var sendBtn = document.getElementById('sendBtn');
                if (sendBtn.classList.contains('active')) {
                    sendBtn.click();
                }
            }
        });
    }

    // ==================== 发送按钮 ====================
    function initSendButton() {
        var sendBtn = document.getElementById('sendBtn');
        if (!sendBtn) return;

        sendBtn.addEventListener('click', function() {
            if (state.isGenerating) return;
            generateImage();
        });
    }

    function updateSendButton() {
        var sendBtn = document.getElementById('sendBtn');
        var promptInput = document.getElementById('agnesPrompt');
        if (!sendBtn || !promptInput) return;

        var hasContent = promptInput.value.trim().length > 0 || state.uploadedImages.length > 0;
        sendBtn.classList.toggle('active', hasContent && !state.isGenerating);
    }

    // ==================== 图片生成 ====================
    function generateImage() {
        var promptInput = document.getElementById('agnesPrompt');
        var prompt = promptInput.value.trim();

        if (!prompt && state.uploadedImages.length === 0) return;

        state.isGenerating = true;
        updateSendButton();

        // 显示加载状态
        showLoading(true);

        // 构建请求参数
        var sizeMap = {
            '1K': '1024x1024',
            '2K': '2048x2048',
            '4K': '4096x4096'
        };

        var ratio = state.selectedRatio;
        if (ratio === 'auto') ratio = '1:1';

        var size = sizeMap[state.selectedResolution] || '1024x1024';

        // 前端内容预检
        var blockedPatterns = ['nude','naked','裸体','裸露','露点','色情','porn','sexual','topless','nsfw','blood','gore','violence','drug','weapon','gun','bomb','毒品','武器','枪','bikini','泳装','比基尼','内衣','underwear','lingerie','性感','sexy','诱惑','seductive'];
        var promptLower = prompt.toLowerCase();
        for (var i = 0; i < blockedPatterns.length; i++) {
            if (promptLower.indexOf(blockedPatterns[i]) !== -1) {
                showError('提示词包含不当内容，请修改后重试');
                state.isGenerating = false;
                updateSendButton();
                return;
            }
        }

        // 步骤1：AI 增强提示词
        var aiParams = new URLSearchParams({
            action: 'aiphoto_ai_enhance',
            nonce: aiphotoAjax.nonce,
            prompt: prompt || '',
            template: state.currentTemplate || ''
        });

        fetch(aiphotoAjax.url + '?' + aiParams.toString())
            .then(function(r) { return r.json(); })
            .then(function(aiData) {
                var finalPrompt = prompt;
                if (aiData.success && aiData.data && aiData.data.enhanced) {
                    finalPrompt = aiData.data.enhanced;
                    finalPrompt = finalPrompt.replace(/\s*--\w+\s+\S+/g, '').trim();
                }

                // 步骤2：生成图片
                var formData = new FormData();
                formData.append('action', 'aiphoto_generate');
                formData.append('nonce', aiphotoAjax.nonce);
                formData.append('prompt', finalPrompt);
                formData.append('user_prompt', prompt);
                formData.append('size', size);
                formData.append('ratio', ratio);

                return fetch(aiphotoAjax.url, {
                    method: 'POST',
                    body: formData
                });
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showResult(data.data);
                } else {
                    showError(data.data.message || '生成失败，请重试');
                }
            })
            .catch(function(err) {
                console.error('AIPhoto错误:', err);
                showError('网络错误，请重试');
            })
            .finally(function() {
                state.isGenerating = false;
                updateSendButton();
                showLoading(false);
            });
    }

    function showLoading(show) {
        var existing = document.querySelector('.agnes-loading');
        if (show) {
            if (existing) {
                existing.classList.add('visible');
                return;
            }
            var loader = document.createElement('div');
            loader.className = 'agnes-loading visible';
            loader.innerHTML = '<div class="loading-spinner"></div>' +
                '<div class="loading-text">AI 正在生成图片...</div>' +
                '<div class="loading-progress"><div class="loading-progress-bar" id="progressBar"></div></div>';
            document.getElementById('agnesContent').appendChild(loader);
        } else {
            if (existing) existing.remove();
        }
    }

    function showResult(data) {
        hideExistingResult();

        var section = document.createElement('div');
        section.className = 'agnes-result-section visible';
        section.id = 'resultSection';

        var saveStatus = data.save_status || 'success';
        var warningHtml = '';
        if (saveStatus === 'failed') {
            warningHtml = '<div class="result-error" style="margin-top:12px;background:#fffbeb;border-color:#fde68a;color:#92400e;">⚠️ 图片已生成但保存到网站失败：<br>' + (data.save_error || '未知错误') + '</div>';
        }

        section.innerHTML =
            '<div class="result-header">' +
                '<h3>生成结果</h3>' +
                '<div class="result-actions">' +
                    '<button class="result-action-btn" id="downloadBtn">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
                        '下载原图' +
                    '</button>' +
                    '<button class="result-action-btn" id="copyPromptBtn">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>' +
                        '复制提示词' +
                    '</button>' +
                '</div>' +
            '</div>' +
            '<div class="result-image-container">' +
                '<img id="resultImage" src="' + (data.gallery_url || data.url) + '" alt="AI生成图片">' +
            '</div>' +
            warningHtml;

        document.getElementById('agnesContent').appendChild(section);

        // 绑定按钮
        var dlBtn = document.getElementById('downloadBtn');
        if (dlBtn) {
            dlBtn.addEventListener('click', function() {
                window.open(data.url, '_blank');
            });
        }

        var cpBtn = document.getElementById('copyPromptBtn');
        if (cpBtn && data.prompt) {
            cpBtn.addEventListener('click', function() {
                navigator.clipboard.writeText(data.prompt).then(function() {
                    var orig = cpBtn.textContent.trim();
                    cpBtn.textContent = '✓ 已复制';
                    setTimeout(function() { cpBtn.textContent = orig; }, 2000);
                });
            });
        }

        // 平滑滚动到结果
        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function showError(msg) {
        hideExistingResult();

        var section = document.createElement('div');
        section.className = 'agnes-result-section visible';
        section.id = 'resultSection';
        section.innerHTML = '<div class="result-error">' + msg + '</div>';

        document.getElementById('agnesContent').appendChild(section);

        setTimeout(function() {
            var el = document.getElementById('resultSection');
            if (el) el.remove();
        }, 5000);
    }

    function hideExistingResult() {
        var existing = document.getElementById('resultSection');
        if (existing) existing.remove();
    }

    // ==================== 发现 Tabs ====================
    function initDiscoverTabs() {
        var tabs = document.querySelectorAll('.discover-tab');
        if (tabs.length === 0) return;

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                tabs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
            });
        });
    }

    // ==================== 主题切换 ====================
    function initThemeToggle() {
        var saved = localStorage.getItem('aiphoto_theme');
        if (saved) {
            document.documentElement.setAttribute('data-theme', saved);
        }
    }

    // ==================== 最近作品 ====================
    function initRecentWorks() {
        // 从 PHP 注入的最近生成数据
        var recentGrid = document.querySelector('.gen-recent-grid');
        if (!recentGrid) return;

        // 当有输入时，显示左侧最近作品列表
        var promptInput = document.getElementById('agnesPrompt');
        if (!promptInput) return;

        promptInput.addEventListener('input', function() {
            var sidebar = document.querySelector('.recent-work-list');
            if (this.value.trim().length > 0) {
                if (sidebar) sidebar.classList.add('visible');
            } else {
                if (sidebar) sidebar.classList.remove('visible');
            }
        });
    }

})();
