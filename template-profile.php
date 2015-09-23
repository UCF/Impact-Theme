<?php
/**
 * Template Name: Profile
 */
	get_header();
	the_post();
	$html_id = get_post_meta( $post->ID, 'page_html', True );
	$html_file = wp_get_attachment_url( $html_id );

	// Set a timeout
	$opts = array('http' => array(
						'method'  => 'GET',
						'timeout' => FEED_FETCH_TIMEOUT
	));
	$context = stream_context_create($opts);

	$html = wptexturize( do_shortcode( file_get_contents( $html_file, false, $context ) ) );
?>
<main class="page page-profile" id="<?php echo $post->post_name; ?>">
	<?php echo $html; ?>
</main>

<?php get_footer(); ?>
