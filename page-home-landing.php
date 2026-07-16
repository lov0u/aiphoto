<?php
/**
 * 模板名称: 首页落地页
 * 描述: AIPhoto 首页 - 品牌展示 + 功能引导
 */

get_header();

$settings = aiphoto_get_settings();
?>

<!-- ============================================
     HERO SECTION — 品牌落地页风格
     ============================================ -->
<section class="lp-hero" id="hero">
    <div class="lp-hero-bg">
        <div class="lp-blob lp-blob--1"></div>
        <div class="lp-blob lp-blob--2"></div>
        <div class="lp-blob lp-blob--3"></div>
    </div>

    <div class="container lp-hero-inner">
        <!-- 引擎标识 -->
        <div class="lp-engine-badge">
            <span class="lp-engine-dot"></span>
            基于 Agnes AI 引擎
        </div>

        <h1 class="lp-hero-title">
            用 AI 创造<br>
            <span class="lp-gradient-text">免费可商用的精美图片</span>
        </h1>

        <p class="lp-hero-desc">
            输入描述文字，AI 即刻为你生成高质量图片。所有图片自动压缩为 WebP 格式，永久保存在你的媒体库，免费下载，无版权限制。
        </p>

        <div class="lp-hero-actions">
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'generate' ) ) ?: home_url( '/generate/' ) ); ?>" class="lp-btn lp-btn--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                立即生成图片
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'gallery' ) ) ?: home_url( '/gallery/' ) ); ?>" class="lp-btn lp-btn--secondary">
                浏览媒体库
            </a>
        </div>

        <!-- 三大卖点 -->
        <div class="lp-features-mini">
            <div class="lp-feature-item">
                <span class="lp-feature-num">免费</span>
                <span class="lp-feature-label">完全免费</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">无版权</span>
                <span class="lp-feature-label">自由使用</span>
            </div>
            <div class="lp-feature-item">
                <span class="lp-feature-num">WebP</span>
                <span class="lp-feature-label">自动优化</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     功能介绍
     ============================================ -->
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
                <p>自动压缩为 WebP 格式，保存到媒体库，随时下载</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     功能亮点
     ============================================ -->
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                <h3>自动压缩</h3>
                <p>所有图片自动压缩为 WebP 格式，控制在 150KB 以内</p>
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

<!-- ============================================
     画廊预览
     ============================================ -->
<section class="lp-section lp-gallery-preview">
    <div class="container">
        <h2 class="lp-section-title">最新作品</h2>
        <p class="lp-section-desc">探索社区用户的精彩创作</p>

        <?php
        $gallery_query = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => 6,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ) );
        ?>

        <?php if ( $gallery_query->have_posts() ) : ?>
        <div class="lp-preview-grid">
            <?php foreach ( $gallery_query->posts as $attach_id ) :
                $thumb_url = wp_get_attachment_image_src( $attach_id, 'medium' );
                $full_url  = wp_get_attachment_image_src( $attach_id, 'full' );
                $title     = get_the_title( $attach_id );
                ?>
                <a href="<?php echo esc_url( $full_url[0] ); ?>" class="lp-preview-item" target="_blank" rel="noopener">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="lp-empty-gallery">
            <p>暂无图片，去生成你的第一张 AI 图片吧！</p>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'generate' ) ) ?: home_url( '/generate/' ) ); ?>" class="lp-btn lp-btn--primary">开始创作</a>
        </div>
        <?php endif; ?>

        <div style="text-align:center;margin-top:40px;">
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'gallery' ) ) ?: home_url( '/gallery/' ) ); ?>" class="lp-btn lp-btn--secondary">
                浏览全部作品 →
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     CTA 区域
     ============================================ -->
<section class="lp-section lp-cta">
    <div class="container">
        <h2>准备好开始创作了吗？</h2>
        <p>输入你的想法，让 AI 帮你实现</p>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'generate' ) ) ?: home_url( '/generate/' ) ); ?>" class="lp-btn lp-btn--primary lp-btn--large">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            立即生成图片
        </a>
    </div>
</section>

<?php get_footer(); ?>
