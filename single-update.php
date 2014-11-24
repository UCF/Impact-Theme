<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>

<div class="container">
	<div class="row page-content" id="<?=$post->post_name?>">
		<div class="span12">
			<article>
				<h1><?php the_title();?></h1>
				<?php the_content();?>
			</article>
		</div>
	</div>
</div>

<?php get_template_part('includes/below-the-fold'); ?>

<?php get_footer();?>
