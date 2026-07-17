<?php
/**
 * 模板名称: 首页
 * 描述: AIPhoto 统一入口 - 根据 ?page= 参数路由到不同页面
 */

get_header();

$settings = aiphoto_get_settings();

// 路由判断：优先 ?page= 参数，其次 URL slug
$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'home';
if ( $page === 'home' && isset( $_SERVER['REQUEST_URI'] ) ) {
    $uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
    $slug = basename( rtrim( $uri, '/' ) );
    $valid = array( 'generate', 'gallery', 'video', 'chat' );
    if ( in_array( $slug, $valid, true ) ) {
        $page = $slug;
    }
}
?>

<!-- ============================================
     HOME - 首页落地页
     ============================================ -->
<?php if ( 'home' === $page ) : ?>

<section class="lp-hero" id="hero">
    <div class="lp-hero-bg">
        <div class="lp-blob lp-blob--1"></div>
        <div class="lp-blob lp-blob--2"></div>
        <div class="lp-blob lp-blob--3"></div>
    </div>

    <div class="container lp-hero-inner">
        <!-- 精品图片轮播 -->
        <?php
        $featured_dir = get_template_directory() . '/assets/images/featured';
        $featured_images = array();
        if ( is_dir( $featured_dir ) ) {
            $files = scandir( $featured_dir );
            $allowed = array( 'jpg', 'jpeg', 'png', 'webp', 'gif' );
            foreach ( $files as $file ) {
                if ( $file === '.' || $file === '..' ) continue;
                $ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
                if ( in_array( $ext, $allowed, true ) ) {
                    $featured_images[] = get_template_directory_uri() . '/assets/images/featured/' . rawurlencode( $file );
                }
            }
        }
        if ( ! empty( $featured_images ) ) :
        ?>
        <div class="featured-carousel" id="featuredCarousel">
            <?php foreach ( $featured_images as $idx => $img_url ) : ?>
            <div class="featured-slide<?php echo $idx === 0 ? ' active' : ''; ?>">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="精品展示" loading="eager">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="featured-dots" id="featuredDots"></div>
        <?php endif; ?>

        <div class="lp-engine-badges">
            <span class="lp-engine-badge">
                <span class="lp-engine-dot"></span>
                Agnes AI
            </span>
            <span class="lp-engine-badge">
                <span class="lp-engine-dot lp-engine-dot--orange"></span>
                Stable Diffusion
            </span>
            <span class="lp-engine-badge">
                <span class="lp-engine-dot lp-engine-dot--green"></span>
                Flux.1
            </span>
            <span class="lp-engine-badge">
                <span class="lp-engine-dot lp-engine-dot--purple"></span>
                Midjourney
            </span>
            <span class="lp-engine-badge">
                <span class="lp-engine-dot lp-engine-dot--red"></span>
                Grok
            </span>
            <span class="lp-engine-badge">
                <span class="lp-engine-dot lp-engine-dot--cyan"></span>
                Ideogram
            </span>
        </div>

        <h1 class="lp-hero-title">
              创造 AI 奇迹<br>
            <span class="lp-gradient-text">免费可商用的精美图片</span>
        </h1>

        <p class="lp-hero-desc">
            输入描述文字，AI 即刻为你生成高质量图片。支持 10+ 艺术风格，4K 超清画质，所有图片永久保存在你的媒体库，免费下载，无版权限制。
        </p>

        <div class="lp-hero-actions">
            <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                立即生成图片
            </a>
            <a href="https://aiphoto.ra0.cn/gallery" class="lp-btn lp-btn--secondary">
                浏览媒体库
            </a>
        </div>

        <div class="lp-features-mini">
            <div class="lp-feature-item">
                <span class="lp-feature-num">AI</span>
                <span class="lp-feature-label">智能生成</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">4K</span>
                <span class="lp-feature-label">超清画质</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">10+</span>
                <span class="lp-feature-label">艺术风格</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">100%</span>
                <span class="lp-feature-label">免费使用</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">API</span>
                <span class="lp-feature-label">开放接口</span>
            </div>
        </div>
    </div>
</section>

<style>
.featured-carousel {
    position: relative;
    width: calc(100% + 210px);
    margin-left: -105px;
    height: 385px;
    overflow: hidden;
    margin-bottom: 12px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.featured-slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.8s ease;
}

.featured-slide.active {
    opacity: 1;
}

.featured-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
}

.featured-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
}

.featured-dot.active {
    background: var(--color-primary);
    width: 24px;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .featured-carousel {
        width: calc(100% + 40px);
        margin-left: -20px;
        height: 220px;
        border-radius: 12px;
    }
}
</style>

<script>
(function() {
    var carousel = document.getElementById('featuredCarousel');
    var dotsWrap = document.getElementById('featuredDots');
    if (!carousel || !dotsWrap) return;

    var slides = carousel.querySelectorAll('.featured-slide');
    if (slides.length === 0) return;

    var current = 0;
    var timer = null;

    slides.forEach(function(_, i) {
        var dot = document.createElement('span');
        dot.className = 'featured-dot' + (i === 0 ? ' active' : '');
        dot.onclick = function() { goTo(i); };
        dotsWrap.appendChild(dot);
    });

    function goTo(index) {
        slides[current].classList.remove('active');
        dotsWrap.children[current].classList.remove('active');
        current = index;
        slides[current].classList.add('active');
        dotsWrap.children[current].classList.add('active');
        resetTimer();
    }

    function next() { goTo((current + 1) % slides.length); }

    function resetTimer() {
        clearInterval(timer);
        timer = setInterval(next, 4000);
    }

    carousel.addEventListener('mouseenter', function() { clearInterval(timer); });
    carousel.addEventListener('mouseleave', resetTimer);

    resetTimer();
})();
</script>

<section class="lp-section lp-how-it-works">
    <div class="container">
        <h2 class="lp-section-title">如何工作</h2>
        <p class="lp-section-desc">只需三步，即可生成属于你的 AI 图片</p>
        <div class="lp-steps">
            <div class="lp-step">
                <div class="lp-step-num">1</div>
                <h3>输入描述</h3>
                <p>用文字描述你想要的图片内容，越详细越好</p>
            </div>
            <div class="lp-step">
                <div class="lp-step-num">2</div>
                <h3>AI 生成</h3>
                <p>基于 Agnes AI 引擎，几秒钟内生成高质量图片</p>
            </div>
            <div class="lp-step">
                <div class="lp-step-num">3</div>
                <h3>下载保存</h3>
                <p>自动保存到媒体库，随时下载</p>
            </div>
        </div>
    </div>
</section>

<section class="lp-section lp-showcase">
    <div class="container">
        <h2 class="lp-section-title">功能亮点</h2>
        <p class="lp-section-desc">强大的 AI 图片创作工具，满足你的所有需求</p>
        <div class="lp-cards">
            <div class="lp-card">
                <div class="lp-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <h3>文生图</h3>
                <p>输入文字描述，AI 自动生成对应图片，支持多种风格和尺寸</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <h3>图生图</h3>
                <p>上传参考图片，AI 基于参考图进行风格转换和优化</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                </div>
                <h3>媒体库管理</h3>
                <p>图片自动保存到 WordPress 媒体库，方便管理和复用</p>
            </div>
        </div>
    </div>
</section>

<section class="lp-section lp-gallery-preview">
    <div class="container">
        <h2 class="lp-section-title">最新作品</h2>
        <p class="lp-section-desc">探索社区用户的精彩创作</p>
        <?php
        $gp = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => 6,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ) );
        if ( $gp->have_posts() ) :
        ?>
        <div class="lp-preview-grid">
            <?php foreach ( $gp->posts as $aid ) :
                $tu = wp_get_attachment_image_src( $aid, 'medium' );
                $fu = wp_get_attachment_image_src( $aid, 'full' );
                $tt = get_the_title( $aid );
                $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true );
                ?>
                <div class="lp-preview-item masonry-item" data-full="<?php echo esc_url( $fu[0] ); ?>" data-title="<?php echo esc_attr( $tt ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>" style="cursor:pointer;">
                    <?php if ( $tu ) : ?>
                        <img src="<?php echo esc_url( $tu[0] ); ?>" alt="<?php echo esc_attr( $tt ); ?>" loading="lazy">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="lp-empty-gallery">
            <p>暂无图片，去生成你的第一张 AI 图片吧！</p>
            <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary">开始创作</a>
        </div>
        <?php endif; wp_reset_postdata(); ?>
        <div style="text-align:center;margin-top:40px;">
            <a href="https://aiphoto.ra0.cn/gallery" class="lp-btn lp-btn--secondary">浏览全部作品 →</a>
        </div>
    </div>
</section>

<section class="lp-section lp-cta">
    <div class="container">
        <h2>准备好开始创作了吗？</h2>
        <p>输入你的想法，让 AI 帮你实现</p>
        <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary lp-btn--large">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            立即生成图片
        </a>
    </div>
</section>

<!-- ============================================
     GENERATE - 图片生成页面
     ============================================ -->
<?php elseif ( 'generate' === $page ) : ?>

<section class="gen-page" id="gen-page">
    <div class="container" style="padding-top: 20px;">
        <div class="gen-layout">
            <div class="gen-panel gen-panel--editor">
                <div class="gen-panel-header">
                    <h1>生成图片</h1>
                    <p class="gen-panel-desc">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="vertical-align: -2px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        请勿生成侵犯他人隐私的照片，生成的图片将保存至服务器
                    </p>
                </div>

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

                <form class="gen-form" id="generatorForm" novalidate>
                    <div class="gen-input-group">
                        <label for="generatorPrompt" class="gen-label">提示词</label>
                        <textarea id="generatorPrompt" class="gen-textarea" rows="2"
                                  placeholder="描述你想生成的图片，例如：夕阳下的宁静湖面..."
                                  required maxlength="500"></textarea>
                    </div>

                    <!-- 快捷选项 -->
                    <div class="gen-template-row" style="margin-bottom:8px;">
                        <label class="gen-label">快捷选项</label>
                        <div class="gen-template-tags" id="templateTags" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                            <span class="gen-template-tag" data-template="beauty" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">美颜</span>
                            <span class="gen-template-tag" data-template="soft_light" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">柔光</span>
                            <span class="gen-template-tag" data-template="hd" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">高清</span>
                            <span class="gen-template-tag" data-template="film" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">胶片感</span>
                            <span class="gen-template-tag" data-template="magazine" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">杂志风</span>
                            <span class="gen-template-tag" data-template="dreamy" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">梦幻</span>
                        </div>
                    </div>

                    <div class="gen-btn-row">
                        <button type="button" class="gen-mode-btn gen-mode-btn--active" id="txt2imgBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                            文生图
                        </button>
                        <button type="button" class="gen-mode-btn" id="img2imgToggle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            图生图
                        </button>
                    </div>

                    <div class="gen-img2img-area" id="img2imgArea" style="display:none;">
                        <div class="gen-img2img-content">
                            <div id="img2imgPreview" class="gen-img2img-preview"></div>
                            <input type="file" id="img2imgInput" accept="image/*" multiple style="display:none;">
                            <button type="button" class="gen-select-file-btn" id="img2imgUploadBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                选择参考图片（可选，最多 4 张）
                            </button>
                        </div>
                    </div>

                    <div class="gen-options-row">
                        <div class="gen-option">
                            <label for="generatorEffect" class="gen-label">视觉特效</label>
                            <select id="generatorEffect" class="gen-select">
                                <option value="cinematic" selected>电影级</option>
                                <option value="pixel-art">像素风</option>
                                <option value="cartoon">卡通</option>
                                <option value="3d-render">3D 渲染</option>
                                <option value="watercolor">水彩</option>
                                <option value="oil-painting">油画</option>
                                <option value="anime">动漫</option>
                                <option value="photorealistic">照片级写实</option>
                                <option value="cyberpunk">赛博朋克</option>
                                <option value="fantasy">奇幻</option>
                            </select>
                        </div>
                        <div class="gen-option">
                            <label for="generatorLens" class="gen-label">镜头角度</label>
                            <select id="generatorLens" class="gen-select">
                                <option value="wide-angle">广角镜头</option>
                                <option value="macro">微距镜头</option>
                                <option value="birdseye">鸟瞰视角</option>
                                <option value="eye-level">平视视角</option>
                                <option value="low-angle">仰视视角</option>
                                <option value="close-up" selected>特写</option>
                                <option value="portrait">人像镜头</option>
                                <option value="panoramic">全景</option>
                            </select>
                        </div>
                        <div class="gen-option">
                            <label for="generatorSize" class="gen-label">清晰度</label>
                            <select id="generatorSize" class="gen-select">
                                <option value="1K">1K (1024×1024)</option>
                                <option value="2K" selected>2K (2048×2048)</option>
                                <option value="3K">3K (3072×3072)</option>
                                <option value="4K">4K (4096×4096)</option>
                            </select>
                        </div>
                        <div class="gen-option">
                            <label for="generatorRatio" class="gen-label">比例</label>
                            <select id="generatorRatio" class="gen-select">
                                <option value="16:9" selected>16:9 宽屏</option>
                                <option value="1:1">1:1 正方形</option>
                                <option value="9:16">9:16 竖屏</option>
                                <option value="4:3">4:3 横版</option>
                                <option value="3:4">3:4 竖版</option>
                                <option value="3:2">3:2</option>
                                <option value="2:3">2:3</option>
                                <option value="21:9">21:9 超宽</option>
                            </select>
                        </div>
                    </div>

                </form>

                    <!-- 进度框 + 开始/停止按钮（始终显示，在表单外面） -->
                    <div id="genProgressWrap" style="margin-top:8px;display:flex;align-items:stretch;gap:8px;">
                        <div id="genProgressBox" style="flex:1;border:1px solid #e0e0e0;border-radius:8px;padding:8px 12px;background:#fafafa;height:66px;overflow-y:auto;display:flex;align-items:center;justify-content:center;">
                            <div id="genWelcomeMsg" style="font-size:14px;font-weight:600;color:#8b5cf6;">输入描述，点击开始生成图片</div>
                        </div>
                        <button type="button" id="genStartBtn" style="flex-shrink:0;padding:0 24px;background:#ef4444;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">开始</button>
                    </div>

                <div class="gen-result" id="generatorResult">
                    <div class="gen-result-header"><h3>生成结果</h3></div>
                    <div class="gen-result-image">
                        <img id="resultImage" src="" alt="">
                    </div>
                    <div class="gen-result-actions">
                        <button class="gen-action-btn" id="downloadBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            下载原图
                        </button>
                        <button class="gen-action-btn" id="copyPromptBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            复制提示词
                        </button>
                    </div>
                    <div class="status-message error" id="errorMessage" style="display:none;" role="alert"></div>
                </div>
            </div>

            <div class="gen-panel gen-panel--recent">
                <div class="gen-panel-header">
                    <h2>最近生成</h2>
                    <a href="https://aiphoto.ra0.cn/gallery" class="gen-view-all">查看全部</a>
                </div>
                <div class="gen-recent-grid" id="recentGrid">
                    <?php
                    $rg = new WP_Query( array(
                        'post_type'      => 'attachment',
                        'post_status'    => 'inherit',
                        'post_mime_type' => 'image',
                        'posts_per_page' => 6,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'fields'         => 'ids',
                    ) );
                    if ( $rg->have_posts() ) :
                        foreach ( $rg->posts as $aid ) :
                            $tu = wp_get_attachment_image_src( $aid, 'medium' );
                            $fu = wp_get_attachment_image_src( $aid, 'full' );
                            $tt = get_the_title( $aid );
                            ?>
                            <?php $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true ); ?>
                            <div class="gen-recent-item" data-full="<?php echo esc_url( $fu[0] ); ?>" data-title="<?php echo esc_attr( $tt ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                                <?php if ( $tu ) : ?>
                                    <img src="<?php echo esc_url( $tu[0] ); ?>" alt="<?php echo esc_attr( $tt ); ?>" loading="lazy">
                                <?php endif; ?>
                            </div>
                        <?php endforeach;
                    else : ?>
                        <div class="gen-empty-recent"><p>还没有生成图片</p></div>
                    <?php endif; wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     GALLERY - 画廊页面
     ============================================ -->
<?php elseif ( 'gallery' === $page ) : ?>

<section class="gallery-page" id="gallery-page">
    <div class="container" style="padding-top: 20px;">
        <div class="gallery-header">
            <h1 class="gallery-title-glass">
                <span class="gallery-title-text">灵感库</span>
            </h1>
            <p>探索 AI 创作的无限灵感</p>

            <div class="gallery-search">
                <form class="gallery-search-form" id="gallerySearchForm">
                    <svg class="gallery-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" id="gallerySearchInput" class="gallery-search-input" placeholder="搜索图片（输入提示词关键词）" maxlength="100">
                    <button type="submit" class="gallery-search-btn">搜索</button>
                </form>
                <div class="gallery-search-hint">
                    <span id="gallerySearchResult"></span>
                </div>
            </div>
        </div>

        <?php
        $gs = $settings['gallery_source'] ?? 'media';
        $pp = absint( $settings['gallery_per_page'] );

        if ( 'cpt' === $gs ) {
            $gq = new WP_Query( array(
                'post_type'      => 'ai_photo',
                'posts_per_page' => $pp,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
        } else {
            $gq = new WP_Query( array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'post_mime_type' => 'image',
                'posts_per_page' => $pp,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ) );
        }

        if ( $gq->have_posts() ) :
        ?>
        <div class="masonry-grid" id="masonryGrid">
            <?php
            if ( 'cpt' === $gs ) :
                while ( $gq->have_posts() ) : $gq->the_post();
                    if ( has_post_thumbnail() ) :
                        $full_url = get_the_post_thumbnail_url( null, 'full' );
                        $user_prompt = get_post_meta( get_the_ID(), '_aiphoto_user_prompt', true ) ?: get_post_meta( get_the_ID(), '_aiphoto_prompt', true );
                        ?>
                        <article class="masonry-item" data-full="<?php echo esc_url( $full_url ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                            <div class="masonry-link lightbox-trigger">
                                <?php the_post_thumbnail( 'aiphoto-large' ); ?>
                            </div>
                        </article>
                    <?php endif;
                endwhile;
            else :
                foreach ( $gq->posts as $aid ) :
                    $tu = wp_get_attachment_image_src( $aid, 'aiphoto-large' );
                    $fu = wp_get_attachment_image_src( $aid, 'full' );
                    $tt = get_the_title( $aid );
                    $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true );
                    ?>
                    <article class="masonry-item" data-full="<?php echo esc_url( $fu[0] ); ?>" data-title="<?php echo esc_attr( $tt ?: 'AI 图片' ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                        <div class="masonry-link lightbox-trigger">
                            <?php if ( $tu ) : ?>
                                <img src="<?php echo esc_url( $tu[0] ); ?>" alt="<?php echo esc_attr( $tt ); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach;
            endif;
            ?>
        </div>
        <?php wp_reset_postdata(); ?>
        
        <!-- 查看更多按钮 -->
        <div class="load-more-wrapper">
            <button class="load-more-btn" id="loadMoreBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                查看更多
            </button>
        </div>
        
        <?php else : ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>暂无图片</h3>
            <p>去生成你的第一张 AI 图片吧！</p>
            <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary">开始创作</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================
     VIDEO - 视频生成预留页
     ============================================ -->
<?php elseif ( 'video' === $page ) : ?>

<section class="coming-soon-page">
    <div class="container">
        <div class="coming-soon-content">
            <div class="coming-soon-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </div>
            <h1>视频生成</h1>
            <p class="coming-soon-desc">AI 视频生成功能正在开发中，敬请期待。</p>
            <p class="coming-soon-note">后续将支持文生视频、图生视频等多种模式。</p>
            <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary">去生成图片</a>
        </div>
    </div>
</section>

<!-- ============================================
     CHAT - AI 聊天预留页
     ============================================ -->
<?php elseif ( 'chat' === $page ) : ?>

<section class="coming-soon-page">
    <div class="container">
        <div class="coming-soon-content">
            <div class="coming-soon-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h1>AI 聊天</h1>
            <p class="coming-soon-desc">AI 智能对话功能正在开发中，敬请期待。</p>
            <p class="coming-soon-note">后续将支持多轮对话、智能助手等多种交互模式。</p>
            <a href="https://aiphoto.ra0.cn/generate" class="lp-btn lp-btn--primary">去生成图片</a>
        </div>
    </div>
</section>

<?php endif; ?>

<?php get_footer(); ?>
