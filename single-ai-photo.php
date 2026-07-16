<?php
/**
 * 模板名称: 单张图片详情
 * 描述: AI图片详情页
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $prompt_text = get_the_excerpt();
        $thumbnail_url = get_the_post_thumbnail_url( 'aiphoto-xl' );
?>

<style>
    .single-photo-section {
        padding-top: 120px;
        min-height: 100vh;
    }
    .single-photo-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--space-2xl) var(--space-lg);
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-2xl);
    }
    .single-photo-image {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--color-border);
    }
    .single-photo-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    .single-photo-info {
        background: var(--color-bg-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
    }
    .single-photo-info h1 {
        margin-bottom: var(--space-md);
    }
    .single-photo-meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-md);
        margin-bottom: var(--space-lg);
    }
    .meta-tag {
        display: inline-flex;
        align-items: center;
        gap: var(--space-xs);
        padding: var(--space-xs) var(--space-md);
        background: var(--color-bg-muted);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-full);
        font-size: 0.8125rem;
        color: var(--color-foreground-muted);
    }
    .single-photo-prompt {
        background: var(--color-bg-base);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        padding: var(--space-lg);
        margin-bottom: var(--space-lg);
    }
    .single-photo-prompt h3 {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-foreground-dim);
        margin-bottom: var(--space-sm);
    }
    .single-photo-prompt p {
        color: var(--color-foreground);
        font-size: 1rem;
        line-height: 1.6;
    }
    .single-photo-actions {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-sm);
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: var(--space-xs);
        padding: var(--space-sm) var(--space-md);
        color: var(--color-foreground-muted);
        font-size: 0.9375rem;
        transition: color var(--transition-fast);
    }
    .back-link:hover {
        color: var(--color-primary-light);
    }
    .back-link svg {
        width: 18px;
        height: 18px;
    }
</style>

<section class="single-photo-section">
    <div class="container">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'ai_photo' ) ?: home_url( '/' ) ); ?>" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            <?php esc_html_e( '返回画廊', 'aiphoto' ); ?>
        </a>

        <div class="single-photo-container">
            <div class="single-photo-image">
                <?php if ( $thumbnail_url ) : ?>
                    <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                         alt="<?php echo esc_attr( get_the_title() ); ?>"
                         loading="eager">
                <?php else : ?>
                    <p><?php esc_html_e( '暂无图片。', 'aiphoto' ); ?></p>
                <?php endif; ?>
            </div>

            <div class="single-photo-info">
                <h1><?php the_title(); ?></h1>

                <div class="single-photo-meta">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'Y年n月j日' ) ); ?>
                    </time>
                    <?php
                    $cats = get_the_terms( get_the_ID(), 'photo_category' );
                    if ( $cats ) :
                        foreach ( $cats as $cat ) :
                            ?>
                            <span class="meta-tag">
                                <?php echo esc_html( $cat->name ); ?>
                            </span>
                        <?php endforeach;
                    endif;

                    $styles = get_the_terms( get_the_ID(), 'photo_style' );
                    if ( $styles ) :
                        foreach ( $styles as $style ) :
                            ?>
                            <span class="meta-tag">
                                <?php echo esc_html( $style->name ); ?>
                            </span>
                        <?php endforeach;
                    endif;
                    ?>
                </div>

                <?php if ( $prompt_text ) : ?>
                    <div class="single-photo-prompt">
                        <h3><?php esc_html_e( '提示词', 'aiphoto' ); ?></h3>
                        <p><?php echo esc_html( $prompt_text ); ?></p>
                    </div>
                <?php endif; ?>

                <div class="single-photo-actions">
                    <?php if ( $thumbnail_url ) : ?>
                        <a href="<?php echo esc_url( $thumbnail_url ); ?>"
                           class="result-btn"
                           download
                           target="_blank"
                           rel="noopener">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:16px;height:16px;">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <?php esc_html_e( '下载原图', 'aiphoto' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    endwhile;
endif;

get_footer();
?>
