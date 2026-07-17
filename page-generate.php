<?php
/**
 * 模板名称: 图片生成
 * 描述: AI 图片生成页面 - Agnes 风格
 */

get_header();

$settings = aiphoto_get_settings();

// 最近生成数据（PHP 注入给 JS）
$recent_imgs = new WP_Query( array(
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'post_mime_type' => 'image',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'fields'         => 'ids',
) );
$recent_data = array();
if ( $recent_imgs->have_posts() ) :
    foreach ( $recent_imgs->posts as $aid ) :
        $tu = wp_get_attachment_image_src( $aid, 'medium' );
        $fu = wp_get_attachment_image_src( $aid, 'full' );
        $tt = get_the_title( $aid );
        $up = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true );
        $recent_data[] = array(
            'thumb' => $tu[0] ?? '',
            'full'  => $fu[0] ?? '',
            'title' => $tt ?: 'AI 图片',
            'prompt'=> $up,
        );
    endforeach;
endif;
wp_reset_postdata();
?>

<?php if ( empty( $settings['api_key'] ) ) : ?>
<div class="api-warning" role="alert">
    <?php
    printf(
        esc_html__( 'API 尚未配置。请%s开始使用。', 'aiphoto' ),
        '<a href="' . esc_url( admin_url( 'admin.php?page=aiphoto-settings' ) ) . '">' . esc_html__( '前往设置', 'aiphoto' ) . '</a>'
    );
    ?>
</div>
<?php endif; ?>

<!-- 全局背景 -->
<div class="agnes-global-bg"></div>

<!-- 主容器 -->
<div class="agnes-app" id="agnesApp">

    <!-- 左侧作品列表（输入后显示） -->
    <aside class="agnes-recent-sidebar" id="agnesRecentSidebar" style="display:none;">
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
            <?php if ( ! empty( $recent_data ) ) : ?>
                <?php foreach ( $recent_data as $r ) : ?>
                <div class="recent-item" data-full="<?php echo esc_url( $r['full'] ); ?>" data-prompt="<?php echo esc_attr( $r['prompt'] ); ?>">
                    <div class="recent-thumb">
                        <?php if ( ! empty( $r['thumb'] ) ) : ?>
                            <img src="<?php echo esc_url( $r['thumb'] ); ?>" alt="<?php echo esc_attr( $r['title'] ); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="recent-info">
                        <div class="recent-title"><?php echo esc_html( $r['title'] ); ?></div>
                        <div class="recent-prompt"><?php echo esc_html( mb_substr( $r['prompt'], 0, 30 ) ); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>

    <!-- 主内容区 -->
    <main class="agnes-main">
        <div class="agnes-content" id="agnesContent">

            <!-- 欢迎态（未输入时显示） -->
            <div class="agnes-welcome" id="agnesWelcome">
                <div class="welcome-text">
                    <h1 class="welcome-title">释放您的创造力，</h1>
                    <h2 class="welcome-subtitle">立即将想法变为现实！</h2>
                </div>
                <div class="welcome-visual">
                    <div class="visual-ring"></div>
                    <div class="visual-ring visual-ring--2"></div>
                    <div class="visual-ring visual-ring--3"></div>
                </div>
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
            <div class="agnes-discover-tabs" id="discoverTabs" style="display:none;">
                <div class="discover-tabs-inner">
                    <button class="discover-tab" data-tab="discover">发现</button>
                    <button class="discover-tab active" data-tab="short">短剧</button>
                </div>
                <a href="#" class="discover-upload">上传短剧</a>
            </div>

            <!-- 底部推荐卡片 -->
            <div class="agnes-recommended" id="recommendedSection" style="display:none;">
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
