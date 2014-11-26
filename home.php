<?php get_header(); the_post();?>
<?php
$home = get_page_by_path('home');
if (!$home) {
	$home = $post; // get something...
}
?>

<main class="home">
	<h1><?php echo $home->post_title; ?></h1>
	<?php echo apply_filters('the_content', $home->post_content); ?>
</main>

<?php get_footer(); ?>
