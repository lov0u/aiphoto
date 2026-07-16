<?php
/**
 * AIPhoto Theme - Main Index Template
 *
 * This file serves as the fallback template for WordPress 6.5+.
 * The theme primarily uses PHP templates (front-page.php, page-gallery.php, etc.)
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :
			if ( is_home() && ! is_front_page() ) :
				echo '<header><h1 class="page-title">' . esc_html__( 'Posts', 'aiphoto' ) . '</h1></header>';
			endif;

			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;

			the_posts_navigation();

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
