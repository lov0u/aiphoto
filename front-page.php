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

