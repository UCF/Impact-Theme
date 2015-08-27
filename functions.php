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
 * Returns the url of the page's header image by the
 * size specified.
 *
 * @param int $feature_id    - post ID of the page with header image
 * @param string $size       - image size registered with Wordpress to fetch the image by
 * @param string $cpt_field  - name (including prefix) of the meta field for the potential overridden image
 * @return string
 **/
function get_header_img($post_id, $size, $cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);
	$thumb = null;
	$generated_thumb = wp_get_attachment_image_src($featured_img_id, $size);
	$custom_thumb = wp_get_attachment_url(get_post_meta($post_id, $cpt_field, true));

	$thumb = $custom_thumb ? $custom_thumb : $generated_thumb[0];
	return preg_replace('/^http(s)?\:/', '', $thumb);
}


/**
 * Output CSS necessary for responsive header images.
 *
 * @param int $post_id        - post ID of the page
 * @param string $d_cpt_field - name (including prefix) of the meta field for the potential overridden image for desktop browsers
 * @param string $t_cpt_field - name (including prefix) of the meta field for the potential overridden image for tablet browsers
 * @param string $m_cpt_field - name (including prefix) of the meta field for the potential overridden image for mobile browsers
 **/
function get_header_image_css($post_id, $lg_cpt_field, $md_cpt_field, $sm_cpt_field, $xs_cpt_field) {

	$header_img_lg = get_header_img($post_id, 'parallax_feature-full', $lg_cpt_field);
	$header_img_md = get_header_img($post_id, 'parallax_feature-desktop', $md_cpt_field);
	$header_img_sm = get_header_img($post_id, 'parallax_feature-tablet', $sm_cpt_field);
	$header_img_xs = get_header_img($post_id, 'parallax_feature-mobile', $xs_cpt_field);

	ob_start();
?>
	<style type="text/css">
		<?php if ($header_img_lg) { ?>
		@media all and (min-width: 1200px) { #photo_<?=$post_id?> { background-image: url('<?php echo $header_img_lg; ?>'); } }
		<?php } ?>
		<?php if ($header_img_md) { ?>
		@media all and (max-width: 1199px) and (min-width: 992px) { #photo_<?=$post_id?> { background-image: url('<?php echo $header_img_md; ?>'); } }
		<?php } ?>
		<?php if ($header_img_sm) { ?>
		@media all and (max-width: 991px) and (min-width: 768px) { #photo_<?=$post_id?> { background-image: url('<?php echo $header_img_sm; ?>'); } }
		<?php } ?>
		<?php if ($header_img_xs) { ?>
		@media all and (max-width: 767px) { #photo_<?=$post_id?> { background-image: url('<?php echo $header__img_xs; ?>'); } }
		<?php } ?>
	</style>
	<!--[if lt IE 9]>
	<style type="text/css">
		#photo_<?=$post_id?> { background-image: url('<?php echo $header_img_lg; ?>'); }
	</style>
	<![endif]-->
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


/**
 * Displays a full-width grid list of impact profiles, using pages in the
 * profile-list menu.
 **/
function display_profile_list() {
	$menu_name = 'profile-list';

    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[$menu_name] ) ) {
		$menu = wp_get_nav_menu_object( $locations[$menu_name] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );
	}

	ob_start();
?>
<nav class="profile-list">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col-nopad">
				<div class="profile-list-item profile-item-lead">
					<div class="profile-item-inner">
						<div class="profile-item-content">
							<h2 class="profile-list-title"><?php echo get_theme_option( 'profile_list_title' ); ?></h2>
							<div class="profile-list-description">
								<?php echo get_theme_option( 'profile_list_description' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
			if ( $menu_items ) :
				foreach ( $menu_items as $key => $menu_item ):
					$profile_img_bw = wp_get_attachment_url( get_post_meta( $menu_item->object_id, 'page_profile_list_bw', true ) );
					$profile_img_c = wp_get_attachment_url( get_post_meta( $menu_item->object_id, 'page_profile_list_c', true ) );

					$profile_title = $menu_item->title;
					$profile_title_alt = get_post_meta( $menu_item->object_id, 'page_subtitle', true );
			?>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col-nopad">
				<article class="profile-list-item">
					<div class="profile-img profile-img-bw" style="background-image: url(<?php echo $profile_img_bw; ?>);" ></div>
					<div class="profile-img profile-img-c" style="background-image: url(<?php echo $profile_img_c; ?>);" ></div>
					<a class="profile-item-inner" href="<?php echo $menu_item->url; ?>">
						<div class="profile-item-content">
							<h3 class="profile-item-title">
								<?php if ( $profile_title !== $profile_title_alt ): ?>
									<span class="profile-item-name"><?php echo $profile_title; ?></span>
								<?php endif; ?>

								<span class="profile-item-subtitle"><?php echo $profile_title_alt; ?></span>
							</h3>
						</div>
					</a>
				</article>
			</div>
			<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</nav>
<?php
	return ob_get_clean();
}


/**
 * Displays the organization address and phone number defined in Theme Options.
 **/
function display_address() {
	$address = get_theme_option( 'organization_address' );
	$phone = get_theme_option( 'site_contact_phone' );

	ob_start();
?>

	<?php if ( $address ): ?>
	<address>
		<?php echo nl2br( wptexturize( $address ) ); ?>

		<?php if ( $phone ): ?>
		<br>
		<a href="tel:<?php echo preg_replace( '/[^0-9]/', '', $phone ); ?>">
			<?php echo $phone; ?>
		</a>
		<?php endif; ?>
	</address>
	<?php endif; ?>

<?php
	return ob_get_clean();
}


/**
 * Enqueues page-specific css and js.
 **/
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
add_action( 'wp_enqueue_scripts', 'enqueue_custom_files' );


/**
 * Adds various allowed tags to WP's allowed tags list.
 *
 * Add elements and attributes to this list if WordPress' filters refuse to
 * parse those elems/attributes, or shortcodes within them, as expected.
 *
 * Adding 'source' and its 'src' attr fixes usage of <source src="[media...]">
 * after the WP 4.2.3 Shortcode API change.
 **/
global $allowedposttags;
function add_kses_whitelisted_attributes( $allowedposttags, $context ) {
	if ( $context == 'post' ) {
		$allowedposttags['source'] = array(
			'sizes' => true,
			'src' => true,
			'srcset' => true,
			'type' => true,
			'media' => true
		);
	}
	return $allowedposttags;
}
add_filter( 'wp_kses_allowed_html', 'add_kses_whitelisted_attributes', 10, 2 );

?>
