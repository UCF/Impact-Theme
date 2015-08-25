<?php
/**
 * Template Name: Profile
 */
	get_header(); 
	the_post();
	enqueue_custom_files();
	$html_id = get_post_meta( $post->ID, 'page_html', True );
	$html_file = wp_get_attachment_url( $html_id );
	$html = apply_filters('the_content', file_get_contents( $html_file ) );
?>
<main class="page page-profile" id="<?echo $post->post_name?>">
	<?php echo $html; ?>
</main>

<?php get_footer();?>