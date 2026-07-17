/**
 * AIPhoto - 主 JavaScript
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initThemeToggle();
        initMobileMenu();
        initHeaderScroll();
        initGeneratorForm();
        initFilterTabs();
        initStaggerAnimations();
        initLightbox();
        initImg2Img();
        initLoadMore();
        initGallerySearch();
    });

    // 主题切换
    function initThemeToggle() {
        var toggle = document.getElementById('themeToggle');
        if (!toggle) return;
        var saved = localStorage.getItem('aiphoto_theme');
        if (saved) document.documentElement.setAttribute('data-theme', saved);
        else if (window.matchMedia('(prefers-color-scheme: light)').matches)
            document.documentElement.setAttribute('data-theme', 'light');

        toggle.addEventListener('click', function() {
            var cur = document.documentElement.getAttribute('data-theme') || 'dark';
            var next = cur === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('aiphoto_theme', next);
        });
    }

    // 移动端菜单
    function initMobileMenu() {
        var toggle = document.querySelector('.menu-toggle');
        var nav = document.querySelector('.main-navigation');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function() {
            var open = nav.classList.toggle('is-open');
            toggle.classList.toggle('active');
            toggle.setAttribute('aria-expanded', open);
        });

        nav.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                nav.classList.remove('is-open');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                nav.classList.remove('is-open');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
    }

    // 头部滚动
    function initHeaderScroll() {
        var header = document.querySelector('.site-header');
        if (!header) return;
        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    header.classList.toggle('scrolled', window.scrollY > 50);
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // ==================== 图生图 ====================
    var img2imgFiles = [];

    function initImg2Img() {
        var toggleBtn = document.getElementById('img2imgToggle');
        var txt2imgBtn = document.getElementById('txt2imgBtn');
        var area = document.getElementById('img2imgArea');
        var fileInput = document.getElementById('img2imgInput');
        var uploadBtn = document.getElementById('img2imgUploadBtn');

        if (!toggleBtn || !area) return;

        // 文生图按钮
        if (txt2imgBtn) {
            txt2imgBtn.addEventListener('click', function() {
                area.style.display = 'none';
                txt2imgBtn.classList.add('gen-mode-btn--active');
                toggleBtn.classList.remove('gen-mode-btn--active');
            });
        }

        // 图生图按钮
        toggleBtn.addEventListener('click', function() {
            var hidden = area.style.display === 'none';
            area.style.display = hidden ? 'block' : 'none';
            toggleBtn.classList.add('gen-mode-btn--active');
            if (txt2imgBtn) txt2imgBtn.classList.remove('gen-mode-btn--active');
            if (hidden) fileInput.click();
        });

        uploadBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            var files = Array.from(this.files);
            files.forEach(function(file) {
                if (img2imgFiles.length >= 4) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    img2imgFiles.push(e.target.result);
                    renderImg2ImgPreview();
                };
                reader.readAsDataURL(file);
            });
            fileInput.value = '';
        });
    }

    function renderImg2ImgPreview() {
        var container = document.getElementById('img2imgPreview');
        if (!container) return;
        container.innerHTML = '';
        img2imgFiles.forEach(function(src, idx) {
            var div = document.createElement('div');
            div.style.position = 'relative';
            div.innerHTML = '<img src="' + src + '" class="preview-thumb" alt=""><span class="remove-btn" data-idx="' + idx + '">✕</span>';
            container.appendChild(div);
        });

        container.querySelectorAll('.remove-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                img2imgFiles.splice(parseInt(this.dataset.idx), 1);
                renderImg2ImgPreview();
            });
        });
    }

    // ==================== 图片生成 ====================
    function initGeneratorForm() {
        var form = document.getElementById('generatorForm');
        var input = document.getElementById('generatorPrompt');
        var btn = document.getElementById('generateBtn');
        var btnText = document.getElementById('btnText');
        var result = document.getElementById('generatorResult');
        var resultImage = document.getElementById('resultImage');
        var errorMessage = document.getElementById('errorMessage');
        var sizeSelect = document.getElementById('generatorSize');
        var ratioSelect = document.getElementById('generatorRatio');

        if (!form || !input || !btn) return;

        input.addEventListener('input', function() {
            btn.disabled = this.value.trim().length === 0 && img2imgFiles.length === 0;
        });

        // 预设模板点击（来自 GPT Image 2 Skill）
        // 只记录模板选择，不填充输入框，后台静默生效
        var currentTemplate = ''; // 当前选中的模板key
        var templateTags = document.getElementById('templateTags');
        if (templateTags) {
            templateTags.addEventListener('click', function(e) {
                var tag = e.target.closest('.gen-template-tag');
                if (!tag) return;
                var key = tag.getAttribute('data-template');
                // 高亮/取消选中
                var allTags = templateTags.querySelectorAll('.gen-template-tag');
                if (currentTemplate === key) {
                    // 再次点击取消选中
                    currentTemplate = '';
                    tag.style.background = '#f0f0f0';
                    tag.style.color = '';
                } else {
                    allTags.forEach(function(t) { t.style.background = '#f0f0f0'; t.style.color = ''; });
                    tag.style.background = '#6c5ce7';
                    tag.style.color = '#fff';
                    currentTemplate = key;
                }
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (isGenerating) return;
            var prompt = input.value.trim();
            if (!prompt && img2imgFiles.length === 0) return;

            // 前端内容预检（第1层防护）
            var blockedPatterns = ['nude','naked','裸体','裸露','露点','色情','porn','sexual','topless','nsfw','blood','gore','violence','drug','weapon','gun','bomb','毒品','武器','枪','bikini','泳装','比基尼','内衣','underwear','lingerie','性感','sexy','诱惑','seductive'];
            var promptLower = prompt.toLowerCase();
            for (var i = 0; i < blockedPatterns.length; i++) {
                if (promptLower.indexOf(blockedPatterns[i]) !== -1) {
                    showError('提示词包含不当内容，请修改后重试');
                    return;
                }
            }

            setLoading(true);
            hideError();
            if (result) result.classList.remove('has-image');

            // 保存完整生成状态
            var genState = {
                prompt: prompt,
                userPrompt: prompt,
                size: sizeSelect ? sizeSelect.value : '',
                ratio: ratioSelect ? ratioSelect.value : '',
                effect: document.getElementById('generatorEffect') ? document.getElementById('generatorEffect').value : '',
                lens: document.getElementById('generatorLens') ? document.getElementById('generatorLens').value : '',
                time: Date.now()
            };
            localStorage.setItem('aiphoto_pending_gen', JSON.stringify(genState));

            doGenerate(genState);
        });

        var genPollTimer = null;
        var genAbortController = null;

        function doGenerate(state) {
            genAbortController = new AbortController();
            var btnText = document.getElementById('btnText');
            var progressBox = document.getElementById('genProgressBox');

            // 清空并显示进度框
            function showProgress() {
                if (progressBox) {
                    progressBox.style.display = 'block';
                    progressBox.innerHTML = '';
                }
            }

            // 追加一条进度消息
            function addProgress(text) {
                if (progressBox) {
                    var line = document.createElement('div');
                    line.textContent = '> ' + text;
                    progressBox.appendChild(line);
                    progressBox.scrollTop = progressBox.scrollHeight;
                }
            }

            showProgress();
            addProgress('开始生成...');
            addProgress('AI 正在分析提示词...');

            // 第一步：先调用 AI 增强（快速，单独请求）
            var aiParams = new URLSearchParams({
                action: 'aiphoto_ai_enhance',
                nonce: aiphotoAjax.nonce,
                prompt: state.prompt || '',
                effect: state.effect || '',
                lens: state.lens || '',
                template: currentTemplate || ''
            });

            fetch(aiphotoAjax.url + '?' + aiParams.toString())
                .then(function(r) { return r.json(); })
                .then(function(aiData) {
                    setProgress('🎨 生成图片中...');

                    // 第二步：用增强后的提示词调用图片生成
                    var formData = new FormData();
                    formData.append('action', 'aiphoto_generate');
                    formData.append('nonce', aiphotoAjax.nonce);
                    formData.append('prompt', aiData.success ? aiData.data.enhanced : state.prompt);
                    formData.append('user_prompt', state.userPrompt || state.prompt || '转换图片');
                    formData.append('size', state.size || '');
                    formData.append('ratio', state.ratio || '');
                    formData.append('effect', state.effect || '');
                    formData.append('lens', state.lens || '');
                    formData.append('template', currentTemplate || '');

                    return fetch(aiphotoAjax.url, { method: 'POST', body: formData, signal: genAbortController.signal });
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    localStorage.removeItem('aiphoto_pending_gen');
                    if (data.success) {
                        showSuccess(data.data);
                    } else {
                        showError(data.data.message || aiphotoAjax.i18n.error);
                    }
                })
                .catch(function(err) {
                    if (err.name === 'AbortError') {
                        localStorage.removeItem('aiphoto_pending_gen');
                        setLoading(false);
                        return;
                    }
                    console.error('AIPhoto错误:', err);
                    showError(aiphotoAjax.i18n.error);
                })
                .finally(function() { setLoading(false); });
        }

        // 轮询检查图片是否已生成
        function startPolling(state) {
            if (genPollTimer) clearInterval(genPollTimer);
            var attempts = 0;
            var maxAttempts = 15; // 最多轮询 30 秒（每 2 秒一次）

            genPollTimer = setInterval(function() {
                attempts++;
                if (attempts > maxAttempts) {
                    clearInterval(genPollTimer);
                    setLoading(false);
                    localStorage.removeItem('aiphoto_pending_gen');
                    return;
                }

                var fd = new FormData();
                fd.append('action', 'aiphoto_check_generation');
                fd.append('nonce', aiphotoAjax.nonce);
                fd.append('prompt', state.prompt);
                fd.append('since', state.time);

                fetch(aiphotoAjax.url, { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success && data.data.found) {
                            clearInterval(genPollTimer);
                            localStorage.removeItem('aiphoto_pending_gen');
                            setLoading(false);
                            // 映射字段名匹配 showSuccess
                            showSuccess({
                                gallery_url: data.data.url,
                                url: data.data.original || data.data.url,
                                prompt: data.data.prompt
                            });
                        }
                    })
                    .catch(function() {});
            }, 2000);
        }

        // 页面加载时恢复未完成的生成状态
        (function restorePendingGen() {
            var pending = localStorage.getItem('aiphoto_pending_gen');
            if (!pending) return;
            try { pending = JSON.parse(pending); } catch (e) { localStorage.removeItem('aiphoto_pending_gen'); return; }

            // 超过 2 分钟直接清理
            if (Date.now() - pending.time > 120000) {
                localStorage.removeItem('aiphoto_pending_gen');
                return;
            }

            // 恢复表单状态
            if (pending.prompt) input.value = pending.prompt;
            if (pending.size && sizeSelect) sizeSelect.value = pending.size;
            if (pending.ratio && ratioSelect) ratioSelect.value = pending.ratio;
            if (pending.effect) {
                var effEl = document.getElementById('generatorEffect');
                if (effEl) effEl.value = pending.effect;
            }
            if (pending.lens) {
                var lensEl = document.getElementById('generatorLens');
                if (lensEl) lensEl.value = pending.lens;
            }

            // 显示正在生成状态
            setLoading(true);
            hideError();
            if (result) result.classList.remove('has-image');

            // 开始轮询等待结果
            startPolling(pending);
        })();

        var isGenerating = false;

        function setLoading(on) {
            isGenerating = on;
            if (on) {
                btn.disabled = true;
                btnText.textContent = '生成中';
                // 创建停止按钮到容器内
                var stopBtn = document.getElementById('stopGenBtn');
                if (!stopBtn) {
                    stopBtn = document.createElement('button');
                    stopBtn.type = 'button';
                    stopBtn.id = 'stopGenBtn';
                    stopBtn.textContent = '停止生成';
                    stopBtn.onclick = function() { stopGeneration(); };
                    btn.parentNode.appendChild(stopBtn);
                }
                stopBtn.style.cssText = 'margin-left:12px;padding:14px 24px;background:#ef4444;color:#fff;border:none;border-radius:var(--radius-md);font-size:1rem;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;';
                // 移动端停止按钮占满宽度
                if (window.innerWidth <= 768) {
                    stopBtn.style.cssText = 'margin-left:0;margin-top:8px;padding:14px 24px;background:#ef4444;color:#fff;border:none;border-radius:var(--radius-md);font-size:1rem;font-weight:600;cursor:pointer;font-family:inherit;width:100%;display:block;text-align:center;box-sizing:border-box;';
                    btn.parentNode.style.flexDirection = 'column';
                } else {
                    btn.parentNode.style.flexDirection = 'row';
                }
            } else {
                btn.disabled = input.value.trim().length === 0 && img2imgFiles.length === 0;
                btnText.textContent = '生成';
                var stopBtn = document.getElementById('stopGenBtn');
                if (stopBtn) stopBtn.style.display = 'none';
                btn.parentNode.style.flexDirection = 'row';
            }
        }

        function stopGeneration() {
            if (genAbortController) genAbortController.abort();
            if (genPollTimer) clearInterval(genPollTimer);
            localStorage.removeItem('aiphoto_pending_gen');
            setLoading(false);
        }

        function showSuccess(data) {
            setLoading(false);
            if (result && resultImage) {
                // 预览用压缩版（加载快），下载用原图
                resultImage.src = data.gallery_url || data.url;
                resultImage.alt = data.prompt || 'AI生成图片';
                result.classList.add('has-image');

                var dl = document.getElementById('downloadBtn');
                if (dl) dl.onclick = function() { window.open(data.url, '_blank'); };

                var cp = document.getElementById('copyPromptBtn');
                if (cp && data.prompt) {
                    cp.onclick = function() {
                        navigator.clipboard.writeText(data.prompt).then(function() {
                            var orig = cp.querySelector('span').textContent;
                            cp.querySelector('span').textContent = '已复制！';
                            setTimeout(function() { cp.querySelector('span').textContent = orig; }, 2000);
                        });
                    };
                }

                // 显示保存状态
                if (data.save_status === 'failed') {
                    console.error('AIPhoto 保存失败:', data.save_error);
                    // 显示警告提示
                    var warningDiv = document.createElement('div');
                    warningDiv.className = 'save-warning';
                    warningDiv.style.cssText = 'background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:12px 16px;margin-top:12px;font-size:14px;color:#856404;';
                    warningDiv.innerHTML = '<strong>⚠️ 图片已生成但保存到网站失败：</strong><br>' + data.save_error + '<br><small>图片仍可通过下载按钮保存。</small>';
                    result.appendChild(warningDiv);
                }

                result.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function showError(msg) {
            setLoading(false);
            if (errorMessage) {
                errorMessage.textContent = msg;
                errorMessage.style.display = 'block';
            }
        }

        function hideError() {
            if (errorMessage) errorMessage.style.display = 'none';
        }
    }

    // 筛选
    function initFilterTabs() {
        var tabs = document.querySelectorAll('#filterTabs .filter-tab');
        var items = document.querySelectorAll('.masonry-item');
        if (tabs.length === 0 || items.length === 0) return;

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var filter = this.getAttribute('data-filter');
                tabs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
                items.forEach(function(item) {
                    var cat = item.getAttribute('data-category');
                    if (filter === 'all' || cat === filter) {
                        item.style.display = '';
                        item.classList.add('animate-fade-in');
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('animate-fade-in');
                    }
                });
            });
        });
    }

    // 交错动画
    function initStaggerAnimations() {
        var els = document.querySelectorAll('.stagger-children');
        if ('IntersectionObserver' in window) {
            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-animated');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
            els.forEach(function(el) { obs.observe(el); });
        } else {
            els.forEach(function(el) { el.classList.add('is-animated'); });
        }
    }

    // 灯箱
    var lightboxCreated = false;
    var lightboxEl, lightboxImg, lightboxTitle, lightboxClose;

    function initLightbox() {
        if (lightboxCreated) return; // 避免重复创建

        lightboxEl = document.createElement('div');
        lightboxEl.className = 'lightbox';
        lightboxEl.setAttribute('role', 'dialog');
        lightboxEl.setAttribute('aria-modal', 'true');
        lightboxEl.innerHTML = '<button class="lightbox-close" aria-label="关闭">&times;</button><img class="lightbox-img" src="" alt=""><div class="lightbox-caption"><p class="lightbox-title"></p><span class="lightbox-expand-btn" style="display:none;">展开全部</span></div>';
        document.body.appendChild(lightboxEl);

        lightboxImg = lightboxEl.querySelector('.lightbox-img');
        lightboxTitle = lightboxEl.querySelector('.lightbox-title');
        lightboxClose = lightboxEl.querySelector('.lightbox-close');
        var lightboxExpand = lightboxEl.querySelector('.lightbox-expand-btn');

        // 展开/收起
        lightboxTitle.addEventListener('click', function() {
            this.classList.toggle('expanded');
            lightboxExpand.textContent = this.classList.contains('expanded') ? '收起' : '展开全部';
        });
        lightboxExpand.addEventListener('click', function() {
            lightboxTitle.classList.toggle('expanded');
            this.textContent = lightboxTitle.classList.contains('expanded') ? '收起' : '展开全部';
        });

        // 使用事件委托处理所有图片点击
        document.addEventListener('click', function(e) {
            // 点击 masonry-item 或其子元素
            var item = e.target.closest('.masonry-item');
            if (item) {
                var fullUrl = item.getAttribute('data-full');
                var origPrompt = item.getAttribute('data-original-prompt');
                if (!fullUrl) return;
                e.preventDefault();
                openLightbox(fullUrl, origPrompt);
                return;
            }

            // 最近生成图片点击
            var recentItem = e.target.closest('.gen-recent-item');
            if (recentItem) {
                var fullUrl = recentItem.getAttribute('data-full');
                var origPrompt = recentItem.getAttribute('data-original-prompt');
                if (!fullUrl) return;
                e.preventDefault();
                openLightbox(fullUrl, origPrompt);
                return;
            }
        });

        // 关闭灯箱
        function closeLB() { lightboxEl.classList.remove('is-open'); document.body.style.overflow = ''; }
        lightboxClose.addEventListener('click', closeLB);
        lightboxEl.addEventListener('click', function(e) { if (e.target === lightboxEl) closeLB(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && lightboxEl.classList.contains('is-open')) closeLB(); });

        lightboxCreated = true;
    }

    function openLightbox(src, prompt) {
        lightboxImg.src = src;
        lightboxImg.alt = prompt || '';
        // 过滤：只保留中文字符、数字、中文标点
        var cleanPrompt = (prompt || '').replace(/[a-zA-Z][a-zA-Z\s,.'\-]*/g, '').replace(/,\s*,/g, ',').replace(/^[\s,]+|[\s,]+$/g, '').trim();
        if (cleanPrompt) {
            lightboxTitle.textContent = '提示词：' + cleanPrompt;
        } else {
            lightboxTitle.textContent = prompt ? '提示词：' + prompt : '';
        }
        // 重置展开状态
        lightboxTitle.classList.remove('expanded');
        var lightboxExpand = lightboxEl.querySelector('.lightbox-expand-btn');
        // 超过 2 行（约 60 个字符）显示展开按钮
        if (lightboxTitle.textContent.length > 60) {
            lightboxExpand.style.display = 'inline-block';
        } else {
            lightboxExpand.style.display = 'none';
        }
        lightboxEl.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        lightboxClose.focus();
    }

    // 加载更多画廊图片
    function initLoadMore() {
        var btn = document.getElementById('loadMoreBtn');
        var grid = document.getElementById('masonryGrid');
        if ( !btn || !grid ) return;

        var offset = 12; // 初始已显示数量，与 PHP 查询一致
        var loading = false;

        btn.addEventListener('click', function() {
            if ( loading ) return;
            loading = true;
            btn.classList.add('loading');
            btn.innerHTML = '<span class="spinner"></span> 加载中...';

            var formData = new FormData();
            formData.append('action', 'aiphoto_load_more');
            formData.append('nonce', aiphotoAjax.nonce);
            formData.append('offset', offset);

            fetch(aiphotoAjax.url, { method: 'POST', body: formData })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if ( data.success && data.data.html ) {
                        grid.insertAdjacentHTML('beforeend', data.data.html);
                        offset += 12;
                    } else {
                        btn.innerHTML = '已加载全部';
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'default';
                    }
                })
                .catch(function() {
                    btn.innerHTML = '加载失败，点击重试';
                })
                .finally(function() {
                    loading = false;
                    btn.classList.remove('loading');
                    if ( offset <= 24 ) {
                        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 查看更多';
                    }
                });
        });
    }

    // ==================== 画廊搜索 ====================
    function initGallerySearch() {
        var form = document.getElementById('gallerySearchForm');
        var input = document.getElementById('gallerySearchInput');
        var resultText = document.getElementById('gallerySearchResult');
        var grid = document.getElementById('masonryGrid');
        var loadMoreBtn = document.getElementById('loadMoreBtn');

        if (!form || !grid) return;

        // 原始内容保存
        var originalHTML = grid.innerHTML;
        var originalLoadMoreDisplay = loadMoreBtn ? loadMoreBtn.style.display : '';

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var keyword = input.value.trim();

            if (!keyword) {
                // 清空搜索，恢复原始内容
                grid.innerHTML = originalHTML;
                if (loadMoreBtn) loadMoreBtn.style.display = originalLoadMoreDisplay;
                resultText.textContent = '';
                return;
            }

            // 显示加载中
            grid.innerHTML = '<div class="gallery-loading"><div class="spinner"></div><p>搜索中...</p></div>';
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            resultText.textContent = '';

            fetch(aiphotoAjax.url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=aiphoto_search&nonce=' + encodeURIComponent(aiphotoAjax.nonce) + '&keyword=' + encodeURIComponent(keyword)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    grid.innerHTML = data.data.html;
                    resultText.textContent = '找到 ' + data.data.count + ' 张相关图片';
                    // 重新绑定灯箱
                    initLightbox();
                } else {
                    grid.innerHTML = '<div class="gallery-empty"><p>' + (data.data.message || '没有找到相关图片') + '</p></div>';
                    resultText.textContent = '';
                }
            })
            .catch(function() {
                grid.innerHTML = '<div class="gallery-empty"><p>搜索失败，请重试</p></div>';
                resultText.textContent = '';
            });
        });

        // 按回车搜索
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                form.dispatchEvent(new Event('submit'));
            }
        });
    }
})();
