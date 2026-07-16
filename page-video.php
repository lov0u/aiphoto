<?php
/**
 * 模板名称: 视频生成（即将上线）
 * 描述: AI 视频生成功能预留页
 */

get_header();
?>

<section class="coming-soon-page">
    <div class="container">
        <div class="coming-soon-content">
            <div class="coming-soon-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
            </div>
            <h1>视频生成</h1>
            <p class="coming-soon-desc">AI 视频生成功能正在开发中，敬请期待。</p>
            <p class="coming-soon-note">后续将支持文生视频、图生视频等多种模式。</p>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'generate' ) ) ?: home_url( '/generate/' ) ); ?>" class="lp-btn lp-btn--primary">
                去生成图片
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
