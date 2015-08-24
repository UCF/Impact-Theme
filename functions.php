<?php
require_once('functions/base.php');   			# Base theme functions
require_once('functions/feeds.php');			# Where per theme settings are registered
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

//Add theme-specific functions here.

/**
 * Returns a theme option value or NULL if it doesn't exist
 **/
function get_theme_option($key) {
	global $theme_options;
	return isset($theme_options[$key]) ? $theme_options[$key] : NULL;
}


/**
 * Disable the standard wysiwyg editor for this theme to prevent markup from being blown away by
 * WYSIWYG users.
 **/
function disable_wysiwyg($c) {
    global $post_type;

    if ('page' == $post_type && get_theme_option('enable_page_wysiwyg') == 1) {
        return false;
    }
    return $c;
}
add_filter('user_can_richedit', 'disable_wysiwyg');


/**
 * Returns the url of the parallax feature's/page's featured image by the
 * size specified.
 *
 * @param int $feature_id    - post ID of the parallax feature or page with featured image
 * @param string $size       - image size registered with Wordpress to fetch the image by
 * @param string $cpt_field  - name (including prefix) of the meta field for the potential overridden image
 * @return string
 **/
function get_parallax_feature_img($post_id, $size, $cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);
	$thumb = null;
	$generated_thumb = wp_get_attachment_image_src($featured_img_id, $size);
	$custom_thumb = wp_get_attachment_url(get_post_meta($post_id, $cpt_field, true));

	$thumb = $custom_thumb ? $custom_thumb : $generated_thumb[0];
	return preg_replace('/^http(s)?\:/', '', $thumb);
}


/**
 * Output CSS necessary for responsive parallax features.
 *
 * @param int $post_id        - post ID of the parallax feature or page
 * @param string $d_cpt_field - name (including prefix) of the meta field for the potential overridden image for desktop browsers
 * @param string $t_cpt_field - name (including prefix) of the meta field for the potential overridden image for tablet browsers
 * @param string $m_cpt_field - name (including prefix) of the meta field for the potential overridden image for mobile browsers
 **/
function get_parallax_feature_css($post_id, $lg_cpt_field, $md_cpt_field, $sm_cpt_field, $xs_cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);

	$featured_img_lg = get_parallax_feature_img($post_id, 'parallax_feature-full', $lg_cpt_field);
	$featured_img_md = get_parallax_feature_img($post_id, 'parallax_feature-desktop', $md_cpt_field);
	$featured_img_sm = get_parallax_feature_img($post_id, 'parallax_feature-tablet', $sm_cpt_field);
	$featured_img_xs = get_parallax_feature_img($post_id, 'parallax_feature-mobile', $xs_cpt_field);

	ob_start();
?>
	<style type="text/css">
		<?php if ($featured_img_lg) { ?>
		@media all and (min-width: 1200px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_lg?>'); } }
		<?php } ?>
		<?php if ($featured_img_md) { ?>
		@media all and (max-width: 1199px) and (min-width: 992px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_md?>'); } }
		<?php } ?>
		<?php if ($featured_img_sm) { ?>
		@media all and (max-width: 991px) and (min-width: 768px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_sm?>'); } }
		<?php } ?>
		<?php if ($featured_img_xs) { ?>
		@media all and (max-width: 767px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_xs?>'); } }
		<?php } ?>
	</style>
	<!--[if lt IE 9]>
	<style type="text/css">
		#photo_<?=$post_id?> { background-image: url('<?=$featured_img_lg?>'); }
	</style>
	<![endif]-->
<?php
	return ob_get_clean();
}


/**
 * Display a subpage parallax header image.
 **/
function get_parallax_page_header($page_id) {
	$page = get_post($page_id);
	ob_start();
	echo get_parallax_feature_css($page_id, 'page_header_lg', 'page_header_md', 'page_header_sm', 'page_header_xs');
	?>
	<section class="parallax-content parallax-header">
		<div class="parallax-photo" id="photo_<?php echo $page_id; ?>"></div>
	</section>
	<?php
	return ob_get_clean();
}


/**
 * Displays a call to action link, using the page link provided in Theme Options.
 **/
function get_cta_link() {
	$link = get_permalink(get_post(get_theme_option('cta'))->ID);
	ob_start();
?>
	<a href="<?php echo $link; ?>"> <?php echo get_theme_option('cta_link_text'); ?></a>
<?php
	return ob_get_clean();
}

/**
 * Displays a call to action link, using the page link provided in Theme Options.
 **/
function get_cta_prefix() {
	$text = get_theme_option('cta_prefix');
	ob_start();
	echo $text;
	return ob_get_clean();
}


/**
 * Hide unused admin tools (Links, Comments, etc)
 **/
function hide_admin_links() {
	remove_menu_page('link-manager.php');
}
add_action('admin_menu', 'hide_admin_links');


/**
* Displays social buttons (Facebook, Twitter, G+) for a post.
* Accepts a post URL and title as arguments.
*
* @return string
* @author Jo Dickson
**/
function display_social($url, $title) {
    $tweet_title = urlencode($title);
    ob_start(); ?>
    <aside class="social">
        <a class="share-facebook" target="_blank" data-button-target="<?php echo $url; ?>" href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>" title="Like this story on Facebook">
            Like "<?php echo $title; ?>" on Facebook
        </a>
        <a class="share-twitter" target="_blank" data-button-target="<?php echo $url; ?>" href="https://twitter.com/intent/tweet?text=<?php echo $tweet_title; ?>&url=<?php echo $url; ?>" title="Tweet this story">
            Tweet "<?php echo $title; ?>" on Twitter
        </a>
        <a class="share-googleplus" target="_blank" data-button-target="<?php echo $url; ?>" href="https://plus.google.com/share?url=<?php echo $url; ?>" title="Share this story on Google+">
            Share "<?php echo $title; ?>" on Google+
        </a>
        <a class="share-linkedin" target="_blank" data-button-target="<?php echo $url; ?>" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $tweet_title; ?>" title="Share this story on Linkedin">
        	Share "<?php echo $title; ?>" on Linkedin
        </a>
        <a class="share-email" target="_blank" data-button-target="<?php echo $url; ?>" href="mailto:?subject=<?php echo $title; ?>&amp;body=Check out this story on www.ucf.edu.%0A%0A<?php echo $url; ?>" title="Share this story in an email">
        	Share "<?php echo $title; ?>" in an email
        </a>
    </aside>
    <?php
    return ob_get_clean();
}

function enqueue_custom_files() {
	global $post;

	$custom_css_id = get_post_meta( $post->ID, 'page_stylesheet', True );
	$custom_js_id = get_post_meta( $post->ID, 'page_javascript', True );

	if ( $custom_css_id ) {
		wp_enqueue_style( $post->post_name.'-stylesheet', wp_get_attachment_url( $custom_css_id ) );
	}

	if ( $custom_js_id ) {
		wp_enqueue_script( $post->post_name.'-javascript', wp_get_attachment_url( $custom_js_id ), null, null, True );
	}
}

?>
