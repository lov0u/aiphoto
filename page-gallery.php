<?php
/**
 * 模板名称: 画廊
 * 描述: 灵感库页面
 */

get_header();
?>

<section class="gallery-page" id="gallery-page">
    <div class="container">
        <div class="gallery-header">
            <h1>灵感库</h1>
            <p>探索 AI 创作的无限灵感</p>
        </div>

        <?php
        $settings = aiphoto_get_settings();
        $gallery_source = $settings['gallery_source'] ?? 'media';
        $per_page = absint( $settings['gallery_per_page'] );

        if ( 'cpt' === $gallery_source ) {
            $gallery_query = new WP_Query( array(
                'post_type'      => 'ai_photo',
                'posts_per_page' => $per_page,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
        } else {
            $gallery_query = new WP_Query( array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'post_mime_type' => 'image',
                'posts_per_page' => $per_page,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ) );
        }

        if ( $gallery_query->have_posts() ) :
        ?>
        <div class="masonry-grid" id="masonryGrid">
            <?php
            if ( 'cpt' === $gallery_source ) :
                while ( $gallery_query->have_posts() ) : $gallery_query->the_post();
                    if ( has_post_thumbnail() ) :
                        $user_prompt = get_post_meta( get_the_ID(), '_aiphoto_user_prompt', true ) ?: get_post_meta( get_the_ID(), '_aiphoto_prompt', true );
                        ?>
                        <article class="masonry-item" data-full="<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                            <div class="masonry-link lightbox-trigger">
                                <?php the_post_thumbnail( 'aiphoto-large' ); ?>
                            </div>
                        </article>
                    <?php endif;
                endwhile;
            else :
                foreach ( $gallery_query->posts as $attach_id ) :
                    $thumb = wp_get_attachment_image_src( $attach_id, 'aiphoto-large' );
                    $full  = wp_get_attachment_image_src( $attach_id, 'full' );
                    $title = get_the_title( $attach_id );
                    $user_prompt = get_post_meta( $attach_id, '_aiphoto_user_prompt', true ) ?: get_post_meta( $attach_id, '_aiphoto_prompt', true );
                    ?>
                    <article class="masonry-item" data-full="<?php echo esc_url( $full[0] ); ?>" data-title="<?php echo esc_attr( $title ?: 'AI 图片' ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                        <div class="masonry-link lightbox-trigger">
                            <?php if ( $thumb ) : ?>
                                <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach;
            endif;
            ?>
        </div>
        <?php wp_reset_postdata(); ?>
        <?php else : ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>暂无图片</h3>
            <p>去生成你的第一张 AI 图片吧！</p>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'generate' ) ) ?: home_url( '/generate/' ) ); ?>" class="lp-btn lp-btn--primary">开始创作</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
