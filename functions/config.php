<?php

/**
 * Responsible for running code that needs to be executed as wordpress is
 * initializing.  Good place to register theme support options, widgets,
 * menu locations, etc.
 *
 * @return void
 * @author Jared Lang
 **/
function __init__(){
	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );

	// These are the image sizes for the header-images on the profiles.
	// Leaving as 'parallax_feature' for backwards compatibility.
	add_image_size('parallax_feature-full', 2000, 1200, true);
	add_image_size('parallax_feature-desktop', 1199, 925, true);
	add_image_size('parallax_feature-tablet', 767, 450, true);
	add_image_size('parallax_feature-mobile', 480, 300, true);
	add_image_size( 'profile-thumbnail', 600, 600, true );

	register_nav_menu('nav-menu', __('Navigation Menu'));
	register_nav_menu( 'profile-list', __( 'Profile List' ) );
	register_nav_menu( 'social-links', __( 'Social Media Profile Links' ) );

	global $timer;
	$timer = Timer::start();

	set_defaults_for_options();
}
add_action('after_setup_theme', '__init__');


/**
 * Register frontend scripts and stylesheets.
 **/
function enqueue_frontend_theme_assets() {
	wp_deregister_script( 'l10n' );

	// Register Config css, js
	foreach( Config::$styles as $style ) {
		if ( !isset( $style['admin'] ) || ( isset( $style['admin'] ) && $style['admin'] !== true ) ) {
			Config::add_css( $style );
		}
	}
	foreach( Config::$scripts as $script ) {
		if ( !isset( $script['admin'] ) || ( isset( $script['admin'] ) && $script['admin'] !== true ) ) {
			Config::add_script( $script );
		}
	}

	// Re-register jquery in document head
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.0.min.js' );
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_frontend_theme_assets' );


/**
 * Register backend scripts and stylesheets.
 **/
function enqueue_backend_theme_assets() {
	// Register Config css, js
	foreach( Config::$styles as $style ) {
		if ( isset( $style['admin'] ) && $style['admin'] == true ) {
			Config::add_css( $style );
		}
	}
	foreach( Config::$scripts as $script ) {
		if ( isset( $script['admin'] ) && $script['admin'] == true ) {
			Config::add_script( $script );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'enqueue_backend_theme_assets' );



# Set theme constants
#define('DEBUG', True);                  # Always on
#define('DEBUG', False);                 # Always off
define('DEBUG', isset($_GET['debug'])); # Enable via get parameter
define('THEME_URL', get_bloginfo('stylesheet_directory'));
define('THEME_ADMIN_URL', get_admin_url());
define('THEME_DIR', get_stylesheet_directory());
define('THEME_INCLUDES_DIR', THEME_DIR.'/includes');
define('THEME_STATIC_URL', THEME_URL.'/static');
define('THEME_IMG_URL', THEME_STATIC_URL.'/img');
define('THEME_JS_URL', THEME_STATIC_URL.'/js');
define('THEME_CSS_URL', THEME_STATIC_URL.'/css');
define('THEME_OPTIONS_GROUP', 'settings');
define('THEME_OPTIONS_NAME', 'theme');
define('THEME_OPTIONS_PAGE_TITLE', 'Theme Options');

$theme_options = get_option(THEME_OPTIONS_NAME);
define('GA_ACCOUNT', isset( $theme_options['ga_account'] ) ? $theme_options['ga_account'] : null );
define('CB_UID', isset( $theme_options['cb_uid'] ) ? $theme_options['cb_uid'] : null );
define('CB_DOMAIN', isset( $theme_options['cb_domain'] ) ? $theme_options['cb_domain'] : null );

# Timeout for data grabbed from feeds
define('FEED_FETCH_TIMEOUT', 10); // seconds


/**
 * Set config values including meta tags, registered custom post types, styles,
 * scripts, and any other statically defined assets that belong in the Config
 * object.
 **/
Config::$custom_post_types = array(
	'Page',
	'Post'
);

Config::$custom_taxonomies = array();

Config::$body_classes = array('default',);


/**
 * Grab array of pages for Config::$theme_settings:
 **/
$pages = get_posts(array('post_type' => 'page', 'posts_per_page' => -1));
$pages_array = array();
foreach ($pages as $page) {
	$pages_array[$page->post_title] = $page->ID;
}

/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions/base.php
 **/
Config::$theme_settings = array(
	'Analytics' => array(
		new TextField(array(
			'name'        => 'Google WebMaster Verification',
			'id'          => THEME_OPTIONS_NAME.'[gw_verify]',
			'description' => 'Example: <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
			'default'     => null,
			'value'       => isset( $theme_options['gw_verify'] ) ? $theme_options['gw_verify'] : null,
		)),
		new TextField(array(
			'name'        => 'Google Analytics Account',
			'id'          => THEME_OPTIONS_NAME.'[ga_account]',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'default'     => null,
			'value'       => isset( $theme_options['ga_account'] ) ? $theme_options['ga_account'] : null,
		)),
	),
	'Events' => array(
		new SelectField(array(
			'name'        => 'Events Max Items',
			'id'          => THEME_OPTIONS_NAME.'[events_max_items]',
			'description' => 'Maximum number of events to display whenever outputting event information.',
			'value'       => isset( $theme_options['events_max_items'] ) ? $theme_options['events_max_items'] : null,
			'default'     => 4,
			'choices'     => array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
			),
		)),
		new TextField(array(
			'name'        => 'Events Calendar URL',
			'id'          => THEME_OPTIONS_NAME.'[events_url]',
			'description' => 'Base URL for the calendar you wish to use. Example: <em>http://events.ucf.edu/mycalendar</em>',
			'value'       => isset( $theme_options['events_url'] ) ? $theme_options['events_url'] : null,
			'default'     => 'http://events.ucf.edu',
		)),
	),
	'Footer' => array(
		new TextField(array(
			'name'        => 'Profile list title',
			'id'          => THEME_OPTIONS_NAME.'[profile_list_title]',
			'description' => 'Text used in the heading for Impact profile lists.',
			'value'       => isset( $theme_options['profile_list_title'] ) ? $theme_options['profile_list_title'] : null,
		)),
		new TextareaField(array(
			'name'        => 'Profile list description',
			'id'          => THEME_OPTIONS_NAME.'[profile_list_description]',
			'description' => 'Description text for Impact profile lists.',
			'value'       => isset( $theme_options['profile_list_description'] ) ? $theme_options['profile_list_description'] : null,
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action Title',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_title]',
			'description' => 'Text used in the heading for the call-to-action section of the footer.',
			'default'     => 'What Will Your Impact Be?',
			'value'       => isset( $theme_options['footer_cta_title'] ) ? $theme_options['footer_cta_title'] : null,
		)),
		new TextareaField(array(
			'name'        => 'Footer Call-to-action Description',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_description]',
			'description' => 'Content displayed in the call-to-action section of the footer.  Accepts HTML and shortcode content.',
			'default'     => 'Help UCF students continue to make an impact on our community today.',
			'value'       => isset( $theme_options['footer_cta_description'] ) ? $theme_options['footer_cta_description'] : null,
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action Text',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_text]',
			'description' => 'Text in the call-to-action button in the footer.',
			'default'     => 'Give Now',
			'value'       => isset( $theme_options['footer_cta_text'] ) ? $theme_options['footer_cta_text'] : null,
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action URL',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_url]',
			'description' => 'Where the call-to-action button in the footer links out to.',
			'value'       => isset( $theme_options['footer_cta_url'] ) ? $theme_options['footer_cta_url'] : null,
		)),
		new TextField(array(
			'name'        => 'Footer 3rd Column Heading Text',
			'id'          => THEME_OPTIONS_NAME.'[footer_col3_heading]',
			'description' => 'Text used in the heading for the 3rd footer column.',
			'value'       => isset( $theme_options['footer_col3_heading'] ) ? $theme_options['footer_col3_heading'] : null,
		)),
		new TextareaField(array(
			'name'        => 'Footer 3rd Column Text',
			'id'          => THEME_OPTIONS_NAME.'[footer_col3_content]',
			'description' => 'Content displayed in the 3rd column of the footer.  Accepts HTML and shortcode content.  Content should be limited to 250 characters (assuming no HTML/shortcode content is added).',
			'value'       => isset( $theme_options['footer_col3_content'] ) ? $theme_options['footer_col3_content'] : null,
		)),
		new RadioField(array(
			'name'        => 'Show UCF logo in 3rd column',
			'id'          => THEME_OPTIONS_NAME.'[footer_col3_logo]',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => isset( $theme_options['footer_col3_logo'] ) ? $theme_options['footer_col3_logo'] : null,
	    )),
	),
	'Search' => array(
		new RadioField(array(
			'name'        => 'Enable Google Search',
			'id'          => THEME_OPTIONS_NAME.'[enable_google]',
			'description' => 'Enable to use the google search appliance to power the search functionality.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => isset( $theme_options['enable_google'] ) ? $theme_options['enable_google'] : null,
	    )),
		new TextField(array(
			'name'        => 'Search Domain',
			'id'          => THEME_OPTIONS_NAME.'[search_domain]',
			'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example: <em>some.domain.com</em>',
			'default'     => null,
			'value'       => isset( $theme_options['search_domain'] ) ? $theme_options['search_domain'] : null,
		)),
		new TextField(array(
			'name'        => 'Search Results Per Page',
			'id'          => THEME_OPTIONS_NAME.'[search_per_page]',
			'description' => 'Number of search results to show per page of results',
			'default'     => 10,
			'value'       => isset( $theme_options['search_per_page'] ) ? $theme_options['search_per_page'] : null,
		)),
	),
	'Site' => array(
		new TextField(array(
			'name'        => 'Home Page Call to Action',
			'id'          => THEME_OPTIONS_NAME.'[home_cta]',
			'description' => 'Call to action text at the bottom of the home page.',
			'value'       => isset( $theme_options['home_cta'] ) ? $theme_options['home_cta'] : null,
		)),
		new TextField(array(
			'name'        => 'Contact Email',
			'id'          => THEME_OPTIONS_NAME.'[site_contact]',
			'description' => 'Contact email address that visitors to your site can use to contact you.',
			'value'       => isset( $theme_options['site_contact'] ) ? $theme_options['site_contact'] : null,
		)),
		new TextField(array(
			'name'        => 'Contact Phone Number',
			'id'          => THEME_OPTIONS_NAME.'[site_contact_phone]',
			'description' => 'Contact phone number that visitors to your site can use to contact you.',
			'value'       => isset( $theme_options['site_contact_phone'] ) ? $theme_options['site_contact_phone'] : null,
		)),
		new TextField(array(
			'name'        => 'Organization Name',
			'id'          => THEME_OPTIONS_NAME.'[organization_name]',
			'description' => 'Your organization\'s name',
			'default'     => 'Impact',
			'value'       => isset( $theme_options['organization_name'] ) ? $theme_options['organization_name'] : null,
		)),
		new TextareaField(array(
			'name'        => 'Organization Address',
			'id'          => THEME_OPTIONS_NAME.'[organization_address]',
			'description' => 'Your organization\'s address',
			'value'       => isset( $theme_options['organization_address'] ) ? $theme_options['organization_address'] : null,
		)),
	),
	'Social' => array(
		new TextField(array(
			'name'        => 'Facebook URL',
			'id'          => THEME_OPTIONS_NAME.'[facebook_url]',
			'description' => 'URL to the facebook page you would like to direct visitors to.  Example: <em>https://www.facebook.com/CSBrisketBus</em>',
			'default'     => null,
			'value'       => isset( $theme_options['facebook_url'] ) ? $theme_options['facebook_url'] : null,
		)),
		new TextField(array(
			'name'        => 'Twitter URL',
			'id'          => THEME_OPTIONS_NAME.'[twitter_url]',
			'description' => 'URL to the twitter user account you would like to direct visitors to.  Example: <em>http://twitter.com/csbrisketbus</em>',
			'value'       => isset( $theme_options['twitter_url'] ) ? $theme_options['twitter_url'] : null,
		)),
	),
	'Web Fonts' => array(
		new TextField(array(
			'name'        => 'Cloud.Typography CSS Key URL',
			'id'          => THEME_OPTIONS_NAME.'[cloud_font_key]',
			'description' => 'The CSS Key provided by Cloud.Typography for this project. <strong>Only include the value in the "href" portion of the link
							tag provided; e.g. "//cloud.typography.com/000000/000000/css/fonts.css".</strong><br/><br/>NOTE: Make sure the Cloud.Typography
							project has been configured to deliver fonts to this site\'s domain.<br/>
							See the <a target="_blank" href="http://www.typography.com/cloud/user-guide/managing-domains">Cloud.Typography docs on managing domains</a> for more info.',
			'default'     => '//cloud.typography.com/730568/803468/css/fonts.css', /* CSS Key relative to PROD project */
			'value'       => isset( $theme_options['cloud_font_key'] ) ? $theme_options['cloud_font_key'] : null,
		)),
	),
	'Settings' => array(
		new RadioField(array(
			'name'        => 'Disable WYSIWYG editor on pages',
			'id'          => THEME_OPTIONS_NAME.'[enable_page_wysiwyg]',
			'description' => 'Disables the WYSIWYG editor for pages, forcing the text editor for all users.  Recommended for this site to avoid custom markup from being blown away by switching editors.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => isset( $theme_options['enable_page_wysiwyg'] ) ? $theme_options['enable_page_wysiwyg'] : null,
	    )),
	),
);


/**
 * If Yoast SEO is activated, assume we're handling ALL SEO-related
 * modifications with it.  Don't add Facebook Opengraph theme options.
 **/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( !is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
	array_unshift( Config::$theme_settings['Social'],
		new RadioField(array(
			'name'        => 'Enable OpenGraph',
			'id'          => THEME_OPTIONS_NAME.'[enable_og]',
			'description' => 'Turn on the opengraph meta information used by Facebook.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => isset( $theme_options['enable_og'] ) ? $theme_options['enable_og'] : null,
	    )),
		new TextField(array(
			'name'        => 'Facebook Admins',
			'id'          => THEME_OPTIONS_NAME.'[fb_admins]',
			'description' => 'Comma seperated facebook usernames or user ids of those responsible for administrating any facebook pages created from pages on this site. Example: <em>592952074, abe.lincoln</em>',
			'default'     => null,
			'value'       => isset( $theme_options['fb_admins'] ) ? $theme_options['fb_admins'] : null,
		))
	);
}


Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);


Config::$styles = array(
	array('admin' => True, 'src' => THEME_CSS_URL.'/admin.css',),
	array('name' => 'theme-styles', 'src' => THEME_CSS_URL.'/style.min.css',),
);

if (!empty($theme_options['cloud_font_key'])) {
	array_push(Config::$styles, array('name' => 'font-cloudtypography', 'src' => $theme_options['cloud_font_key']));
	//array_push(Config::$styles, array('name' => 'font-cloudtypography-admin', 'admin' => True, 'src' => $theme_options['cloud_font_key']));
}


Config::$scripts = array(
	array('admin' => True, 'src' => THEME_JS_URL.'/admin.js',),
	array('name' => 'ucfhb-script', 'src' => '//universityheader.ucf.edu/bar/js/university-header.js?use-1200-breakpoint=1',),
	array('name' => 'theme-script', 'src' => THEME_JS_URL.'/script.min.js',),
);


Config::$metas = array(
	array( 'charset' => 'utf-8' ),
	array( 'http-equiv' => 'X-UA-Compatible', 'content' => 'IE=Edge' ),
	array( 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0' ),
);

if ( isset( $theme_options['gw_verify'] ) && $theme_options['gw_verify'] ) {
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities( $theme_options['gw_verify'] ),
	);
}
