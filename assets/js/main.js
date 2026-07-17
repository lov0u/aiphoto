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
        initSidebar();
        initRecCards();
        initLightbox();
    });

    // ==================== 核心状态 ====================
    var state = {
        hasInput: false,
        isGenerating: false,
        selectedRatio: 'auto',
        selectedResolution: '1K',
        uploadedImages: [],
        sidebarVisible: false
    };

    // ==================== 生成器流程 ====================
    function initGeneratorFlow() {
        var promptInput = document.getElementById('agnesPrompt');
        var welcomeEl = document.getElementById('agnesWelcome');
        var cardEl = document.getElementById('agnesCard');
        var discoverTabs = document.getElementById('discoverTabs');
        var recSection = document.getElementById('recommendedSection');
        var sidebar = document.getElementById('agnesRecentSidebar');
        var newWorkBtn = document.getElementById('newWorkBtn');

        // 监听输入变化
        promptInput.addEventListener('input', function() {
            var hasText = this.value.trim().length > 0;
            var hasImages = state.uploadedImages.length > 0;

            if (hasText || hasImages) {
                if (!state.hasInput) {
                    welcomeEl.style.display = 'none';
                    cardEl.classList.add('visible');
                    if (discoverTabs) discoverTabs.style.display = 'flex';
                    if (recSection) recSection.style.display = 'block';
                    state.hasInput = true;
                }
                // 显示侧边栏
                if (sidebar && !state.sidebarVisible) {
                    sidebar.classList.add('visible');
                    state.sidebarVisible = true;
                }
            } else {
                if (state.hasInput && state.uploadedImages.length === 0) {
                    welcomeEl.style.display = '';
                    cardEl.classList.remove('visible');
                    if (discoverTabs) discoverTabs.style.display = 'none';
                    if (recSection) recSection.style.display = 'none';
                    state.hasInput = false;
                }
                // 隐藏侧边栏
                if (sidebar && state.sidebarVisible) {
                    sidebar.classList.remove('visible');
                    state.sidebarVisible = false;
                }
            }

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

    // ==================== 侧边栏 ====================
    function initSidebar() {
        var sidebar = document.getElementById('agnesRecentSidebar');
        var collapseBtn = document.getElementById('sidebarCollapse');
        if (!collapseBtn) return;

        collapseBtn.addEventListener('click', function() {
            sidebar.classList.remove('visible');
            state.sidebarVisible = false;
        });

        // 点击侧边栏作品
        if (sidebar) {
            sidebar.addEventListener('click', function(e) {
                var item = e.target.closest('.recent-item');
                if (!item) return;
                var prompt = item.getAttribute('data-prompt');
                if (prompt) {
                    var input = document.getElementById('agnesPrompt');
                    if (input) {
                        input.value = prompt;
                        input.dispatchEvent(new Event('input'));
                    }
                }
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

        if (settingsBtn) {
            settingsBtn.addEventListener('click', function() {
                overlay.style.display = 'flex';
                requestAnimationFrame(function() {
                    overlay.classList.add('open');
                });
            });
        }

        function closeSettings() {
            overlay.classList.remove('open');
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 250);
        }

        if (closeBtn) closeBtn.addEventListener('click', closeSettings);
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeSettings();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('open')) {
                closeSettings();
            }
        });

        if (ratioGrid) {
            ratioGrid.addEventListener('click', function(e) {
                var btn = e.target.closest('.ratio-btn');
                if (!btn) return;
                ratioGrid.querySelectorAll('.ratio-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                state.selectedRatio = btn.getAttribute('data-ratio');
            });
        }

        if (resGroup) {
            resGroup.addEventListener('click', function(e) {
                var btn = e.target.closest('.res-btn');
                if (!btn) return;
                resGroup.querySelectorAll('.res-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                state.selectedResolution = btn.getAttribute('data-res');
            });
        }
    }

    // ==================== 上传区域 ====================
    function initUploadArea() {
        var uploadBtn = document.getElementById('uploadPlusBtn');
        var fileInput = document.getElementById('fileInput');

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
            div.innerHTML = '<img src="' + src + '" alt="参考图"><button type="button" class="upload-preview-remove" data-idx="' + idx + '">✕</button>';
            container.appendChild(div);
        });
        container.querySelectorAll('.upload-preview-remove').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                state.uploadedImages.splice(parseInt(this.getAttribute('data-idx')), 1);
                renderUploadPreviews();
                var promptInput = document.getElementById('agnesPrompt');
                if (promptInput) promptInput.dispatchEvent(new Event('input'));
            });
        });
    }

    // ==================== 文本框自适应高度 ====================
    function initTextareaAutoResize() {
        var textarea = document.getElementById('agnesPrompt');
        if (!textarea) return;

        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 180) + 'px';
        });

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
        showLoading(true);

        var sizeMap = { '1K': '1024x1024', '2K': '2048x2048', '4K': '4096x4096' };
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
            template: ''
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

                return fetch(aiphotoAjax.url, { method: 'POST', body: formData });
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
            if (existing) { existing.classList.add('visible'); return; }
            var loader = document.createElement('div');
            loader.className = 'agnes-loading visible';
            loader.innerHTML = '<div class="loading-spinner"></div><div class="loading-text">AI 正在生成图片...</div>';
            document.getElementById('agnesContent').appendChild(loader);
        } else {
            if (existing) existing.remove();
        }
    }

    function showResult(data) {
        var section = document.getElementById('resultSection');
        if (!section) return;

        var saveStatus = data.save_status || 'success';
        var warningHtml = '';
        if (saveStatus === 'failed') {
            warningHtml = '<div class="result-error" style="margin-top:12px;background:#fffbeb;border-color:#fde68a;color:#92400e;">⚠️ 图片已生成但保存到网站失败：<br>' + (data.save_error || '未知错误') + '</div>';
        }

        section.style.display = 'block';
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

        var dlBtn = document.getElementById('downloadBtn');
        if (dlBtn) dlBtn.addEventListener('click', function() { window.open(data.url, '_blank'); });

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

        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function showError(msg) {
        var section = document.getElementById('resultSection');
        if (!section) return;
        section.style.display = 'block';
        section.innerHTML = '<div class="result-error">' + msg + '</div>';
        setTimeout(function() {
            section.style.display = 'none';
            section.innerHTML = '';
        }, 5000);
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

    // ==================== 底部推荐卡片 ====================
    function initRecCards() {
        var scroll = document.getElementById('recScroll');
        if (!scroll) return;

        // 尝试从 featured 目录加载
        var featuredDir = '<?php echo get_template_directory_uri(); ?>/assets/images/featured/';
        var placeholders = ['风景', '人像', '插画', '建筑', '抽象', '动物', '食物', '科技'];
        var colors = ['#e8f5e9','#e3f2fd','#fce4ec','#f3e5f5','#fff3e0','#e0f7fa','#f1f8e9','#ede7f6'];

        placeholders.forEach(function(label, i) {
            var card = document.createElement('div');
            card.className = 'rec-card';
            card.innerHTML = '<div style="width:100%;height:100%;background:' + colors[i] + ';display:flex;align-items:center;justify-content:center;color:#aaa;font-size:13px;">' + label + '</div>';
            scroll.appendChild(card);
        });
    }

    // ==================== 灯箱 ====================
    function initLightbox() {
        var lb = document.getElementById('lightbox');
        if (lb) return; // 已有

        var el = document.createElement('div');
        el.id = 'lightbox';
        el.className = 'lightbox';
        el.innerHTML = '<button class="lightbox-close" aria-label="关闭">&times;</button><img class="lightbox-img" src="" alt=""><div class="lightbox-caption"><p class="lightbox-title"></p></div>';
        document.body.appendChild(el);

        var img = el.querySelector('.lightbox-img');
        var title = el.querySelector('.lightbox-title');
        var close = el.querySelector('.lightbox-close');

        document.addEventListener('click', function(e) {
            var item = e.target.closest('.recent-item');
            if (item) {
                var fullUrl = item.getAttribute('data-full');
                var prompt = item.getAttribute('data-prompt');
                if (fullUrl) {
                    img.src = fullUrl;
                    title.textContent = prompt || '';
                    el.classList.add('is-open');
                    document.body.style.overflow = 'hidden';
                }
            }
        });

        function closeLB() { el.classList.remove('is-open'); document.body.style.overflow = ''; }
        close.addEventListener('click', closeLB);
        el.addEventListener('click', function(e) { if (e.target === el) closeLB(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && el.classList.contains('is-open')) closeLB(); });
    }
})();
