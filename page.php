<?php
	get_header();
	the_post();
	$html_id = get_post_meta( $post->ID, 'page_html', True );
	$html_file = wp_get_attachment_url( $html_id );
	$html = wptexturize( do_shortcode( file_get_contents( $html_file ) ) );
?>
<main class="page page-base" id="<?php echo $post->post_name; ?>">
	<?php
	if ( $html ):
		echo $html;
	else:
	?>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<a class="site-title site-title-alt" href="<?php echo get_site_url(); ?>">
					<?php echo get_theme_option( 'organization_name' ); ?>
					<div class="site-title-divider"></div>
				</a>

				<article class="page-body">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			</div>
		</div>
	</div>

	<?php endif; ?>
</main>

<?php get_footer(); ?>
