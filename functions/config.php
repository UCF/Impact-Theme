<?php

/**
 * Responsible for running code that needs to be executed as wordpress is
 * initializing.  Good place to register scripts, stylesheets, theme elements,
 * etc.
 *
 * @return void
 * @author Jared Lang
 **/
function __init__(){
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	add_image_size('parallax_feature-full', 2000, 1200, true);
	add_image_size('parallax_feature-desktop', 1199, 925, true);
	add_image_size('parallax_feature-tablet', 767, 450, true);
	add_image_size('parallax_feature-mobile', 480, 300, true);
	add_image_size( 'profile-thumbnail', 500, 500, true );
	register_nav_menu('nav-menu', __('Navigation Menu'));
	register_nav_menu( 'profile-list', __( 'Profile List' ) );
	register_nav_menu( 'social-links', __( 'Social Media Profile Links' ) );
	register_sidebar(array(
		'name'          => __('Sidebar'),
		'id'            => 'sidebar',
		'description'   => 'Sidebar found on two column page templates and search pages',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	));
	foreach(Config::$styles as $style){Config::add_css($style);}
	foreach(Config::$scripts as $script){Config::add_script($script);}

	global $timer;
	$timer = Timer::start();

	wp_deregister_script('l10n');
	set_defaults_for_options();
}
add_action('after_setup_theme', '__init__');



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
define('GA_ACCOUNT', $theme_options['ga_account']);
define('CB_UID', $theme_options['cb_uid']);
define('CB_DOMAIN', $theme_options['cb_domain']);

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
			'value'       => $theme_options['gw_verify'],
		)),
		new TextField(array(
			'name'        => 'Google Analytics Account',
			'id'          => THEME_OPTIONS_NAME.'[ga_account]',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'default'     => null,
			'value'       => $theme_options['ga_account'],
		)),
	),
	'Events' => array(
		new SelectField(array(
			'name'        => 'Events Max Items',
			'id'          => THEME_OPTIONS_NAME.'[events_max_items]',
			'description' => 'Maximum number of events to display whenever outputting event information.',
			'value'       => $theme_options['events_max_items'],
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
			'value'       => $theme_options['events_url'],
			'default'     => 'http://events.ucf.edu',
		)),
	),
	'Footer' => array(
		new TextField(array(
			'name'        => 'Profile list title',
			'id'          => THEME_OPTIONS_NAME.'[profile_list_title]',
			'description' => 'Text used in the heading for Impact profile lists.',
			'value'       => $theme_options['profile_list_title'],
		)),
		new TextareaField(array(
			'name'        => 'Profile list description',
			'id'          => THEME_OPTIONS_NAME.'[profile_list_description]',
			'description' => 'Description text for Impact profile lists.',
			'value'       => $theme_options['profile_list_description'],
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action Title',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_title]',
			'description' => 'Text used in the heading for the call-to-action section of the footer.',
			'default'     => 'What Will Your Impact Be?',
			'value'       => $theme_options['footer_cta_title'],
		)),
		new TextareaField(array(
			'name'        => 'Footer Call-to-action Description',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_description]',
			'description' => 'Content displayed in the call-to-action section of the footer.  Accepts HTML and shortcode content.',
			'default'     => 'Help UCF students continue to make an impact on our community today.',
			'value'       => $theme_options['footer_cta_description'],
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action Text',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_text]',
			'description' => 'Text in the call-to-action button in the footer.',
			'default'     => 'Give Now',
			'value'       => $theme_options['footer_cta_text'],
		)),
		new TextField(array(
			'name'        => 'Footer Call-to-action URL',
			'id'          => THEME_OPTIONS_NAME.'[footer_cta_url]',
			'description' => 'Where the call-to-action button in the footer links out to.',
			'value'       => $theme_options['footer_cta_url'],
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
			'value'       => $theme_options['enable_google'],
	    )),
		new TextField(array(
			'name'        => 'Search Domain',
			'id'          => THEME_OPTIONS_NAME.'[search_domain]',
			'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example: <em>some.domain.com</em>',
			'default'     => null,
			'value'       => $theme_options['search_domain'],
		)),
		new TextField(array(
			'name'        => 'Search Results Per Page',
			'id'          => THEME_OPTIONS_NAME.'[search_per_page]',
			'description' => 'Number of search results to show per page of results',
			'default'     => 10,
			'value'       => $theme_options['search_per_page'],
		)),
	),
	'Site' => array(
		new TextField(array(
			'name'        => 'Contact Email',
			'id'          => THEME_OPTIONS_NAME.'[site_contact]',
			'description' => 'Contact email address that visitors to your site can use to contact you.',
			'value'       => $theme_options['site_contact'],
		)),
		new TextField(array(
			'name'        => 'Contact Phone Number',
			'id'          => THEME_OPTIONS_NAME.'[site_contact_phone]',
			'description' => 'Contact phone number that visitors to your site can use to contact you.',
			'value'       => $theme_options['site_contact_phone'],
		)),
		new TextField(array(
			'name'        => 'Organization Name',
			'id'          => THEME_OPTIONS_NAME.'[organization_name]',
			'description' => 'Your organization\'s name',
			'default'     => 'Impact',
			'value'       => $theme_options['organization_name'],
		)),
		new TextareaField(array(
			'name'        => 'Organization Address',
			'id'          => THEME_OPTIONS_NAME.'[organization_address]',
			'description' => 'Your organization\'s address',
			'value'       => $theme_options['organization_address'],
		)),
	),
	'Social' => array(
		new RadioField(array(
			'name'        => 'Enable OpenGraph',
			'id'          => THEME_OPTIONS_NAME.'[enable_og]',
			'description' => 'Turn on the opengraph meta information used by Facebook.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_og'],
	    )),
		new TextField(array(
			'name'        => 'Facebook Admins',
			'id'          => THEME_OPTIONS_NAME.'[fb_admins]',
			'description' => 'Comma seperated facebook usernames or user ids of those responsible for administrating any facebook pages created from pages on this site. Example: <em>592952074, abe.lincoln</em>',
			'default'     => null,
			'value'       => $theme_options['fb_admins'],
		)),
		new TextField(array(
			'name'        => 'Facebook URL',
			'id'          => THEME_OPTIONS_NAME.'[facebook_url]',
			'description' => 'URL to the facebook page you would like to direct visitors to.  Example: <em>https://www.facebook.com/CSBrisketBus</em>',
			'default'     => null,
			'value'       => $theme_options['facebook_url'],
		)),
		new TextField(array(
			'name'        => 'Twitter URL',
			'id'          => THEME_OPTIONS_NAME.'[twitter_url]',
			'description' => 'URL to the twitter user account you would like to direct visitors to.  Example: <em>http://twitter.com/csbrisketbus</em>',
			'value'       => $theme_options['twitter_url'],
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
			'default'     => '//cloud.typography.com/730568/675644/css/fonts.css', /* CSS Key relative to PROD project */
			'value'       => $theme_options['cloud_font_key'],
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
			'value'       => $theme_options['enable_page_wysiwyg'],
	    )),
	),
);

Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);


Config::$styles = array(
	array('admin' => True, 'src' => THEME_CSS_URL.'/admin.css',),
	plugins_url( 'gravityforms/css/forms.css' ),
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
	array('charset' => 'utf-8',),
);
if ($theme_options['gw_verify']){
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities($theme_options['gw_verify']),
	);
}



function jquery_in_header() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
    wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts', 'jquery_in_header');
