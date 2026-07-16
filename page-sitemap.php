<?php
/**
 * Template Name: 网站地图
 * Description: AIPhoto 网站地图
 */

get_header(); ?>

<style>
.sitemap-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 100px 20px 80px;
}
.sitemap-page h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 32px;
    color: #0f172a;
    text-align: center;
}
.sitemap-section {
    margin-bottom: 40px;
}
.sitemap-section h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #7c3aed;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e2e8f0;
}
.sitemap-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.sitemap-list li {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
    transition: all 150ms ease;
}
.sitemap-list li:hover {
    border-color: #7c3aed;
    background: #f5f3ff;
}
.sitemap-list a {
    color: #475569;
    text-decoration: none;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sitemap-list a:hover {
    color: #7c3aed;
}
.sitemap-list .sitemap-icon {
    width: 20px;
    height: 20px;
    color: #94a3b8;
    flex-shrink: 0;
}
.sitemap-images {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.sitemap-images li {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 150ms ease;
}
.sitemap-images li:hover {
    border-color: #7c3aed;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
}
.sitemap-images a {
    display: block;
    text-decoration: none;
}
.sitemap-images img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    display: block;
}
.sitemap-images .img-title {
    padding: 10px 12px;
    font-size: 0.8125rem;
    color: #475569;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sitemap-images .img-prompt {
    padding: 0 12px 10px;
    font-size: 0.75rem;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sitemap-footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
    color: #94a3b8;
    font-size: 0.875rem;
}
</style>

<section class="sitemap-page">
    <h1>网站地图</h1>

    <div class="sitemap-section">
        <h2>主要页面</h2>
        <ul class="sitemap-list">
            <li>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <svg class="sitemap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    探索
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/generate' ) ); ?>">
                    <svg class="sitemap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    创作
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>">
                    <svg class="sitemap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    灵感库
                </a>
            </li>
        </ul>
    </div>

    <div class="sitemap-section">
        <h2>法律信息</h2>
        <ul class="sitemap-list">
            <li>
                <a href="<?php echo esc_url( home_url( '/disclaimer' ) ); ?>">
                    <svg class="sitemap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    免责协议
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/disclaimer' ) ); ?>">
                    <svg class="sitemap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    使用条款
                </a>
            </li>
        </ul>
    </div>

    <div class="sitemap-section">
        <h2>最新图片</h2>
        <?php
        $images = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => 24,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ) );

        if ( $images->have_posts() ) :
        ?>
        <ul class="sitemap-images">
            <?php foreach ( $images->posts as $img_id ) :
                $thumb = wp_get_attachment_image_src( $img_id, 'medium' );
                $full = wp_get_attachment_image_src( $img_id, 'full' );
                $title = get_the_title( $img_id );
                $prompt = get_post_meta( $img_id, '_aiphoto_prompt', true );
                ?>
                <li>
                    <a href="<?php echo esc_url( $full[0] ); ?>" target="_blank" rel="noopener">
                        <?php if ( $thumb ) : ?>
                            <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                        <?php endif; ?>
                        <div class="img-title"><?php echo esc_html( $title ?: 'AI 图片' ); ?></div>
                        <?php if ( $prompt ) : ?>
                            <div class="img-prompt"><?php echo esc_html( mb_substr( $prompt, 0, 30 ) ); ?>...</div>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p style="text-align:center;color:#94a3b8;">暂无图片</p>
        <?php endif; ?>
    </div>

    <div class="sitemap-footer">
        <p>共 <?php echo esc_html( $images->found_posts ); ?> 张 AI 生成图片</p>
    </div>
</section>

<?php get_footer(); ?>
