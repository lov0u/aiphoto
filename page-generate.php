<?php
/**
 * 模板名称: 图片生成
 * 描述: AI 图片生成页面 - Agnes 风格
 */

get_header();

$settings = aiphoto_get_settings();

if ( empty( $settings['api_key'] ) ) : ?>
<div class="api-warning" role="alert">
    <?php
    printf(
        esc_html__( 'API 尚未配置。请%s开始使用。', 'aiphoto' ),
        '<a href="' . esc_url( admin_url( 'admin.php?page=aiphoto-settings' ) ) . '">' . esc_html__( '前往设置', 'aiphoto' ) . '</a>'
    );
    ?>
</div>
<?php endif; ?>

<!-- 主容器 -->
<div class="agnes-app" id="agnesApp">

    <!-- 左侧作品列表（localStorage 驱动） -->
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
                <h1 class="welcome-title">释放您的创造力，</h1>
                <h2 class="welcome-subtitle">立即将想法变为现实！</h2>
            </div>

            <!-- 生成器卡片 -->
            <div class="agnes-generator-card" id="agnesCard">
                <div class="card-inner">

                    <!-- 上传参考素材按钮 -->
                    <div class="card-upload-area" id="cardUploadArea">
                        <button type="button" class="upload-plus-btn" id="uploadPlusBtn">+</button>
                        <input type="file" id="fileInput" accept="image/*" multiple style="display:none;">
                        <div class="upload-previews" id="uploadPreviews"></div>
                    </div>

                    <!-- 文本输入 -->
                    <textarea
                        class="card-textarea"
                        id="agnesPrompt"
                        rows="1"
                        placeholder="上传参考素材、输入文字，请描述你想生成的图片"
                    ></textarea>

                    <!-- 底部工具栏 -->
                    <div class="card-toolbar">
                        <!-- 模式选择 -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn toolbar-btn--dropdown" id="modeDropdownBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                </svg>
                                图片生成
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </div>

                        <!-- 模型选择 -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn toolbar-btn--model" id="modelDropdownBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                                </svg>
                                Agnes Image 2.1 Flash
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </div>

                        <!-- 设置 -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn toolbar-btn--settings" id="settingsBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
                                </svg>
                                设置
                            </button>
                        </div>

                        <!-- @符号 -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn toolbar-btn--at">@</button>
                        </div>

                        <!-- 右侧：积分 + 发送 -->
                        <div class="toolbar-right">
                            <div class="credits-small">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#a8e063" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 6v12M8 10h8M8 14h8"/>
                                </svg>
                                <span>0</span>
                            </div>
                            <button type="button" class="send-btn" id="sendBtn" disabled>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 底部发现 Tabs -->
            <div class="agnes-discover-tabs" id="discoverTabs">
                <div class="discover-tabs-inner">
                    <button class="discover-tab" data-tab="discover">发现</button>
                    <button class="discover-tab active" data-tab="short">短剧</button>
                </div>
                <a href="#" class="discover-upload">上传短剧</a>
            </div>

            <!-- 底部推荐卡片 -->
            <div class="agnes-recommended" id="recommendedSection">
                <div class="rec-scroll" id="recScroll">
                    <!-- JS 动态填充 -->
                </div>
            </div>

            <!-- 生成结果 -->
            <div class="agnes-result-section" id="resultSection" style="display:none;"></div>

        </div><!-- /agnes-content -->
    </main>
</div><!-- /agnes-app -->

<!-- ===== 设置弹窗 ===== -->
<div class="agnes-settings-overlay" id="settingsOverlay" style="display:none;">
    <div class="agnes-settings-panel">
        <h3 class="settings-title">设置</h3>

        <!-- 比例选择 -->
        <div class="settings-section">
            <label class="settings-label">比例</label>
            <div class="ratio-grid" id="ratioGrid">
                <button type="button" class="ratio-btn active" data-ratio="auto">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                    Auto
                </button>
                <button type="button" class="ratio-btn" data-ratio="1:1">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                    1:1
                </button>
                <button type="button" class="ratio-btn" data-ratio="3:4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="3" width="14" height="18" rx="2"/></svg>
                    3:4
                </button>
                <button type="button" class="ratio-btn" data-ratio="4:3">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/></svg>
                    4:3
                </button>
                <button type="button" class="ratio-btn" data-ratio="16:9">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/></svg>
                    16:9
                </button>
                <button type="button" class="ratio-btn" data-ratio="9:16">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/></svg>
                    9:16
                </button>
                <button type="button" class="ratio-btn" data-ratio="2:3">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="2" width="10" height="20" rx="2"/></svg>
                    2:3
                </button>
                <button type="button" class="ratio-btn" data-ratio="3:2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/></svg>
                    3:2
                </button>
                <button type="button" class="ratio-btn" data-ratio="21:9">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="7" width="22" height="10" rx="2"/></svg>
                    21:9
                </button>
            </div>
        </div>

        <!-- 分辨率选择 -->
        <div class="settings-section">
            <label class="settings-label">分辨率</label>
            <div class="resolution-group" id="resolutionGroup">
                <button type="button" class="res-btn active" data-res="1K">1K</button>
                <button type="button" class="res-btn" data-res="2K">2K</button>
                <button type="button" class="res-btn" data-res="4K">4K</button>
            </div>
        </div>

        <!-- 关闭按钮 -->
        <button type="button" class="settings-close" id="settingsClose">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
</div>

<?php get_footer(); ?>
