<?php elseif ( 'generate' === $page ) : ?>

<div class="agnes-global-bg"></div>

<div class="agnes-app" id="agnesApp">

    <!-- 左侧最近作品栏 (localStorage) -->
    <aside class="agnes-recent-sidebar" id="agnesRecentSidebar">
        <div class="recent-sidebar-header">
            <button class="new-work-btn" id="newWorkBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                新作品
            </button>
            <button class="sidebar-collapse-btn" id="sidebarCollapse">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="recent-list" id="recentList">
            <div style="padding:20px 8px;text-align:center;color:#bbb;font-size:12px;">暂无记录</div>
        </div>
    </aside>

    <!-- 主内容区 -->
    <main class="agnes-main">
        <div class="agnes-content" id="agnesContent">

            <!-- 欢迎态 -->
            <div class="agnes-welcome" id="agnesWelcome">
                <h1 class="welcome-title">欢迎来到创作空间</h1>
                <h2 class="welcome-subtitle">输入你的想法，变为现实</h2>

                <!-- 快捷提示词 -->
                <div class="quick-prompts" id="quickPrompts">
                    <button class="quick-prompt-btn" data-prompt="一只戴着墨镜的猫咪坐在沙滩上，夕阳，电影感，高清摄影">🐱 墨镜猫咪</button>
                    <button class="quick-prompt-btn" data-prompt="未来城市天际线，赛博朋克风格，霓虹灯光，雨夜，广角镜头">🌃 赛博城市</button>
                    <button class="quick-prompt-btn" data-prompt="樱花树下的日式庭院，春天花瓣飘落，柔和光线，水彩风格">🌸 日式庭院</button>
                    <button class="quick-prompt-btn" data-prompt="太空站内部，宇航员透过舷窗看地球，科幻，超高清，细节丰富">🚀 太空探索</button>
                    <button class="quick-prompt-btn" data-prompt="古老的东方宫殿，云雾缭绕，仙鹤飞舞，中国水墨画风格">🏯 东方仙境</button>
                    <button class="quick-prompt-btn" data-prompt="可爱的卡通角色，明亮的色彩，扁平化设计，儿童插画风格">🎨 卡通角色</button>
                </div>

                <!-- 底部发现 Tab -->
                <div class="agnes-discover-tabs" id="discoverTabs">
                    <button class="discover-tab active" data-category="all">推荐</button>
                    <button class="discover-tab" data-category="landscape">风景</button>
                    <button class="discover-tab" data-category="portrait">人像</button>
                    <button class="discover-tab" data-category="anime">动漫</button>
                    <button class="discover-tab" data-category="art">艺术</button>
                </div>

                <!-- 底部推荐区 -->
                <div class="agnes-recommended" id="recContainer">
                    <div class="rec-scroll" id="recScroll">
                        <div class="rec-card" data-prompt="一只戴着墨镜的猫咪坐在沙滩上，夕阳，电影感，高清摄影">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#ffecd2,#fcb69f);"></div>
                            <div class="rec-card-text">一只戴着墨镜的猫咪坐在沙滩上...</div>
                        </div>
                        <div class="rec-card" data-prompt="未来城市天际线，赛博朋克风格，霓虹灯光，雨夜，广角镜头">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#a18cd1,#fbc2eb);"></div>
                            <div class="rec-card-text">未来城市天际线，赛博朋克风格...</div>
                        </div>
                        <div class="rec-card" data-prompt="樱花树下的日式庭院，春天花瓣飘落，柔和光线，水彩风格">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#fbc2eb,#a6c1ee);"></div>
                            <div class="rec-card-text">樱花树下的日式庭院...</div>
                        </div>
                        <div class="rec-card" data-prompt="太空站内部，宇航员透过舷窗看地球，科幻，超高清，细节丰富">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#667eea,#764ba2);"></div>
                            <div class="rec-card-text">太空站内部，宇航员透过舷窗...</div>
                        </div>
                        <div class="rec-card" data-prompt="古老的东方宫殿，云雾缭绕，仙鹤飞舞，中国水墨画风格">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#43e97b,#38f9d7);"></div>
                            <div class="rec-card-text">古老的东方宫殿，云雾缭绕...</div>
                        </div>
                        <div class="rec-card" data-prompt="可爱的卡通角色，明亮的色彩，扁平化设计，儿童插画风格">
                            <div class="rec-card-img" style="background:linear-gradient(135deg,#fa709a,#fee140);"></div>
                            <div class="rec-card-text">可爱的卡通角色...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 生成态 (默认隐藏) -->
            <div class="agnes-generator-card" id="agnesGeneratorCard" style="display:none;">
                <div class="gen-topbar">
                    <button class="topbar-collapse-btn" id="topbarCollapse">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <span class="topbar-title">创作</span>
                    <button class="topbar-settings-btn" id="settingsBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </button>
                </div>

                <!-- 上传区 -->
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-plus-btn" id="uploadPlusBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <input type="file" id="fileInput" accept="image/*" multiple style="display:none;">
                    <div class="upload-previews" id="uploadPreviews"></div>
                </div>

                <!-- 提示词输入 -->
                <div class="prompt-wrapper" id="promptWrapper">
                    <textarea id="agnesPrompt" class="agnes-prompt-input" placeholder="描述你想生成的画面..." rows="1"></textarea>
                    <div class="prompt-toolbar">
                        <div class="toolbar-left">
                            <button class="toolbar-btn" id="txt2imgBtn" data-mode="txt2img" title="文生图">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                                文生图
                            </button>
                            <button class="toolbar-btn" id="img2imgToggle" data-mode="img2img" title="图生图">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                图生图
                            </button>
                        </div>
                        <button class="send-btn" id="sendBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </button>
                    </div>
                </div>

                <!-- 图生图参考区 -->
                <div class="img2img-area" id="img2imgArea" style="display:none;">
                    <div class="img2img-hint">已选择参考图片，将基于此图进行生成</div>
                </div>

                <!-- 结果区 -->
                <div class="agnes-result-section" id="resultSection" style="display:none;">
                    <div class="result-image-wrap">
                        <img id="resultImage" src="" alt="AI生成图片" class="result-img">
                    </div>
                    <div class="result-actions">
                        <button class="result-action-btn" id="downloadBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            下载原图
                        </button>
                        <button class="result-action-btn" id="copyPromptBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            复制提示词
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- 设置弹窗 -->
    <div class="agnes-settings-overlay" id="settingsOverlay">
        <div class="agnes-settings-panel">
            <div class="settings-header">
                <h3>设置</h3>
                <button class="settings-close-btn" id="settingsClose">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="settings-body">
                <!-- 比例选择 -->
                <div class="settings-group">
                    <label class="settings-label">比例</label>
                    <div class="ratio-grid" id="ratioGrid">
                        <button class="ratio-btn active" data-ratio="1:1">1:1</button>
                        <button class="ratio-btn" data-ratio="16:9">16:9</button>
                        <button class="ratio-btn" data-ratio="9:16">9:16</button>
                        <button class="ratio-btn" data-ratio="4:3">4:3</button>
                        <button class="ratio-btn" data-ratio="3:4">3:4</button>
                        <button class="ratio-btn" data-ratio="3:2">3:2</button>
                        <button class="ratio-btn" data-ratio="2:3">2:3</button>
                        <button class="ratio-btn" data-ratio="21:9">21:9</button>
                    </div>
                </div>

                <!-- 分辨率 -->
                <div class="settings-group">
                    <label class="settings-label">分辨率</label>
                    <div class="ratio-grid" id="resolutionGroup">
                        <button class="res-btn" data-resolution="1K">1K</button>
                        <button class="res-btn active" data-resolution="2K">2K</button>
                        <button class="res-btn" data-resolution="3K">3K</button>
                        <button class="res-btn" data-resolution="4K">4K</button>
                    </div>
                </div>

                <!-- 艺术风格 -->
                <div class="settings-group">
                    <label class="settings-label">艺术风格</label>
                    <div class="style-tags" id="styleTags">
                        <span class="style-tag active" data-style="photorealistic">照片写实</span>
                        <span class="style-tag" data-style="cinematic">电影感</span>
                        <span class="style-tag" data-style="anime">动漫</span>
                        <span class="style-tag" data-style="3d-render">3D渲染</span>
                        <span class="style-tag" data-style="watercolor">水彩</span>
                        <span class="style-tag" data-style="oil-painting">油画</span>
                        <span class="style-tag" data-style="pixel-art">像素风</span>
                        <span class="style-tag" data-style="cartoon">卡通</span>
                    </div>
                </div>

                <!-- 镜头角度 -->
                <div class="settings-group">
                    <label class="settings-label">镜头角度</label>
                    <div class="style-tags" id="lensTags">
                        <span class="style-tag active" data-lens="close-up">近写</span>
                        <span class="style-tag" data-lens="wide-angle">广角</span>
                        <span class="style-tag" data-lens="macro">微距</span>
                        <span class="style-tag" data-lens="birdseye">鸟瞰</span>
                        <span class="style-tag" data-lens="eye-level">平视</span>
                        <span class="style-tag" data-lens="low-angle">仰视</span>
                        <span class="style-tag" data-lens="portrait">人像</span>
                        <span class="style-tag" data-lens="panoramic">全景</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- API Key 未设置的警告 -->
<?php if ( empty( $settings['api_key'] ) ) : ?>
<style>
.agnes-app { display: none; }
.api-warning {
    margin: 20px auto; max-width: 600px; padding: 16px 20px;
    background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px;
    color: #856404; font-size: 14px;
}
</style>
<div class="api-warning" role="alert">
    <?php
    printf(
        esc_html__( 'API 密钥未设置。请%s开始使用。', 'aiphoto' ),
        '<a href="' . esc_url( admin_url( 'admin.php?page=aiphoto-settings' ) ) . '">' . esc_html__( '前往设置', 'aiphoto' ) . '</a>'
    );
    ?>
</div>
<?php endif; ?>

<!-- ============================================
     GALLERY - 图库页面
     ============================================ -->
<?php elseif ( 'gallery' === $page ) : ?>
