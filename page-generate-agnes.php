<?php
/**
 * Agnes 风格生成页面模板
 * 由 front-page.php include 调用
 */

// 检查 API 配置
$settings = aiphoto_get_settings();
?>

<?php if ( empty( $settings['api_key'] ) ) : ?>
<div class="api-warning" role="alert" style="max-width:960px;margin:20px auto;">
    API 尚未配置。请<a href="<?php echo esc_url( admin_url( 'admin.php?page=aiphoto-settings' ) ) ?>">前往设置</a>开始使用。
</div>
<?php endif; ?>

<!-- 最近生成数据（PHP 注入） -->
<?php
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
            'title' => $tt,
            'prompt'=> $up,
        );
    endforeach;
endif;
wp_reset_postdata();
?>

<script>
window.__aiphoto_recent = <?php echo json_encode( $recent_data ); ?>;
</script>
