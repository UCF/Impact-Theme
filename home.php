<?php
 get_header(); the_post();?>
<?php
$home = get_page_by_path('home');
if (!$home) {
	$home = $post; // Get home post
}

$featured_img_id = get_post_thumbnail_id($home->ID);
$featured_img_f = wp_get_attachment_image_src($featured_img_id, 'parallax_feature-full');
if ($featured_img_f) { ?>
<main class="page home" id="<?php echo $home->post_name?>">
	<?php echo get_parallax_page_header($home->ID); ?>
	<section class="page-content">
		<div class="container">
			<div class="row">
				<div class="span12">
					<h1><?php echo $home->post_title;?></h1>
				</div>
			</div>
		</div>
		<?php echo apply_filters('the_content', $home->post_content); ?>
	</section>
<?php } else { ?>
<main class="page page-base home" id="<?=$home->post_name?>">
	<section class="page-content">
		<div class="container">
			<div class="row">
				<div class="span12">
					<h1><?php echo $home->post_title; ?></h1>
				</div>
			</div>
		</div>
		<?php echo apply_filters('the_content', $home->post_content); ?>
	</section>
<?php } ?>
</main>

<?php $post_type = get_post_type($home->ID);
	if(($stylesheet_id = get_post_meta($home->ID, $post_type.'_stylesheet', True)) !== False
		&& ($stylesheet_url = wp_get_attachment_url($stylesheet_id)) !== False) : ?>
		<link rel='stylesheet' href="<?=$stylesheet_url?>" type='text/css' media='all' />
<?php endif; ?>

<?php get_footer();?>
