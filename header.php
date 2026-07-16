<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo( 'name' ); ?> - AI图片生成与画廊展示平台">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
    <div class="header-inner">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" aria-label="<?php echo esc_attr( bloginfo( 'name' ) ); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-header.png' ); ?>" alt="Aiphoto" class="site-logo-img">
        </a>

        <nav class="main-navigation" role="navigation" aria-label="主菜单">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => '',
                'fallback_cb'    => false,
                'depth'          => 1,
            ) );
            ?>
        </nav>

        <div style="display:flex;align-items:center;gap:4px;">
            <!-- 移动端菜单开关 -->
            <button class="menu-toggle" aria-label="切换菜单" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<!-- 内联脚本：在页面渲染前设置正确的主题，避免图标闪烁 -->
<script>
(function(){
    var t = localStorage.getItem('aiphoto_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();
</script>

<main id="main" class="site-main" role="main">
