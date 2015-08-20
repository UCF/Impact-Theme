<?php
function sc_search_form() {
	ob_start();
	?>
	<div class="search">
		<?get_search_form()?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('search_form', 'sc_search_form');


/**
 * Post search
 *
 * @return string
 * @author Chris Conover
 **/
function sc_post_type_search($params=array(), $content='') {
	$defaults = array(
		'post_type_name'         => 'post',
		'taxonomy'               => 'category',
		'show_empty_sections'    => false,
		'non_alpha_section_name' => 'Other',
		'column_width'           => 'col-md-4',
		'column_count'           => '3',
		'order_by'               => 'title',
		'order'                  => 'ASC',
		'show_sorting'           => True,
		'default_sorting'        => 'term',
		'show_sorting'           => True
	);

	$params = ($params === '') ? $defaults : array_merge($defaults, $params);

	$params['show_empty_sections'] = (bool)$params['show_empty_sections'];
	$params['column_count']        = is_numeric($params['column_count']) ? (int)$params['column_count'] : $defaults['column_count'];
	$params['show_sorting']        = (bool)$params['show_sorting'];

	if(!in_array($params['default_sorting'], array('term', 'alpha'))) {
		$params['default_sorting'] = $default['default_sorting'];
	}

	// Resolve the post type class
	if(is_null($post_type_class = get_custom_post_type($params['post_type_name']))) {
		return '<p>Invalid post type.</p>';
	}
	$post_type = new $post_type_class;

	// Set default search text if the user didn't
	if(!isset($params['default_search_text'])) {
		$params['default_search_text'] = 'Find a '.$post_type->singular_name;
	}

	// Register if the search data with the JS PostTypeSearchDataManager
	// Format is array(post->ID=>terms) where terms include the post title
	// as well as all associated tag names
	$search_data = array();
	foreach(get_posts(array('numberposts' => -1, 'post_type' => $params['post_type_name'])) as $post) {
		$search_data[$post->ID] = array($post->post_title);
		foreach(wp_get_object_terms($post->ID, 'post_tag') as $term) {
			$search_data[$post->ID][] = $term->name;
		}
	}
	?>
	<script type="text/javascript">
		if(typeof PostTypeSearchDataManager != 'undefined') {
			PostTypeSearchDataManager.register(new PostTypeSearchData(
				<?=json_encode($params['column_count'])?>,
				<?=json_encode($params['column_width'])?>,
				<?=json_encode($search_data)?>
			));
		}
	</script>
	<?

	// Split up this post type's posts by term
	$by_term = array();
	foreach(get_terms($params['taxonomy']) as $term) {
		$posts = get_posts(array(
			'numberposts' => -1,
			'post_type'   => $params['post_type_name'],
			'tax_query'   => array(
				array(
					'taxonomy' => $params['taxonomy'],
					'field'    => 'id',
					'terms'    => $term->term_id
				)
			),
			'orderby'     => $params['order_by'],
			'order'       => $params['order']
		));

		if(count($posts) == 0 && $params['show_empty_sections']) {
			$by_term[$term->name] = array();
		} else {
			$by_term[$term->name] = $posts;
		}
	}

	// Split up this post type's posts by the first alpha character
	$by_alpha = array();
	$by_alpha_posts = get_posts(array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => 'title',
		'order'       => 'alpha'
	));
	foreach($by_alpha_posts as $post) {
		if(preg_match('/([a-zA-Z])/', $post->post_title, $matches) == 1) {
			$by_alpha[strtoupper($matches[1])][] = $post;
		} else {
			$by_alpha[$params['non_alpha_section_name']][] = $post;
		}
	}
	ksort($by_alpha);

	if($params['show_empty_sections']) {
		foreach(range('a', 'z') as $letter) {
			if(!isset($by_alpha[strtoupper($letter)])) {
				$by_alpha[strtoupper($letter)] = array();
			}
		}
	}

	$sections = array(
		'post-type-search-term'  => $by_term,
		'post-type-search-alpha' => $by_alpha,
	);

	ob_start();
	?>
	<div class="container">
		<div class="row">
			<div class="span12">
				<div class="post-type-search">
					<div class="post-type-search-header">
						<form class="post-type-search-form" action="." method="get">
							<label style="display:none;">Search</label>
							<input type="text" class="span3" placeholder="<?=$params['default_search_text']?>" />
						</form>
					</div>
					<div class="post-type-search-results "></div>
					<?php if($params['show_sorting']) { ?>
					<div class="btn-group post-type-search-sorting">
						<button class="btn<?if($params['default_sorting'] == 'term') echo ' active';?>"><i class="icon-list-alt"></i></button>
						<button class="btn<?if($params['default_sorting'] == 'alpha') echo ' active';?>"><i class="icon-font"></i></button>
					</div>
					<?php } ?>
	<?php

	foreach($sections as $id => $section) {
		$hide = false;
		switch ( $id ) {
			case 'post-type-search-alpha':
				if ( $params['default_sorting'] == 'term' ) {
					$hide = True;
				}
				break;
			case 'post-type-search-term':
				if ( $params['default_sorting'] == 'alpha' ) {
					$hide = True;
				}
				break;
		}
		?>
					<div class="<?php echo $id; ?>"<? if ( $hide ) echo ' style="display:none;"'; ?>>
						<?php foreach ( $section as $section_title => $section_posts ) { ?>
							<?php if ( count( $section_posts ) > 0 || $params['show_empty_sections'] ) { ?>
								<div>
									<h3><?php echo esc_html( $section_title ); ?></h3>
									<div class="row">
										<? if ( count( $section_posts ) > 0 ) { ?>
											<?php $posts_per_column = ceil( count( $section_posts ) / $params['column_count'] ); ?>
											<?php foreach( range( 0, $params['column_count'] - 1 ) as $column_index ) { ?>
												<?php $start = $column_index * $posts_per_column; ?>
												<?php if ( count( $section_posts ) > $start ) { ?>
												<div class="<?php echo $params['column_width']; ?> resource-list">
													<ul>
													<?php foreach ( array_slice( $section_posts, $start, $posts_per_column ) as $post ) { ?>
														<li class="<?php echo $post_type->get_resource_application( $post ); ?>" data-post-id="<?php echo $post->ID; ?>"><?php echo $post_type->toHTML( $post ); ?></li>
													<?php } ?>
													</ul>
												</div>
												<?php } ?>
											<?php } ?>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
		<?php
	}
	?>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('post-type-search', 'sc_post_type_search');


/**
* Wrap arbitrary text in <blockquote>
**/
function sc_blockquote($attr, $content='') {
	$source = $attr['source'] ? $attr['source'] : null;
	$cite = $attr['cite'] ? $attr['cite'] : null;
	$color = $attr['color'] ? $attr['color'] : null;

	$html = '<blockquote';
	if ($source) {
		$html .= ' class="quote"';
	}

	    if ($color) {
	        $html .= ' style="color: ' . $color . '"';
	    }

	$html .= '><p';
	    if ($color) {
	        $html .= ' style="color: ' . $color . '"';
	    }
	    $html .= '>'.$content.'</p>';

	if ($source || $cite) {
		$html .= '<small';
	        if ($color) {
	            $html .= ' style="color: ' . $color . '"';
	        }
	        $html .= '>';

	if ($source) {
		$html .= $source;
	}
	if ($cite) {
		$html .= '<cite title="'.$cite.'">'.$cite.'</cite>';
	}
		$html .= '</small>';
	}
	$html .= '</blockquote>';

	return $html;
}
add_shortcode('blockquote', 'sc_blockquote');


function sc_comments($attr, $content=null) {
	ob_start();
	if ($attr['title']) {
	?>
		<h2 class="border-bottom comments-title"><?php echo $attr['title'] ?></h2>
	<?php
	}
	comments_template('', true);
	return ob_get_clean();
}
add_shortcode('comments', 'sc_comments');


function sc_comment_form() {
	ob_start();
	if ( comments_open() ) {
		?>
		<!--BEGIN #respond-->
		<div class="border-top<?php if(!$count):?> nocomments<?php endif;?>" id="respond">

			<div class="cancel-comment-reply"><?php cancel_comment_reply_link( 'Cancel Reply' ); ?></div>

			<h3 id="leave-a-reply"><?php comment_form_title( 'Post a Message', 'Leave a message to %s' ); ?></h3>

			<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
			<p id="login-req" class="alert">You must be <a href="<?php echo get_option( 'siteurl' ); ?>/wp-login.php?redirect_to=<?php echo urlencode( get_permalink() ); ?>">logged in</a> to post a comment.</p>
			<?php else : ?>

			<!--BEGIN #comment-form-->
			<form id="comment-form" method="post" action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php">

				<!--BEGIN #form-section-comment-->
				<div id="form-section-comment" class="form-section">
					<textarea name="comment" id="comment" tabindex="2" rows="10"></textarea>
				<!--END #form-section-comment-->
				</div>

				<!--BEGIN #form-section-author-->
				<div id="form-section-author" class="form-section">
					<label for="author"<?php if ( $req ) echo ' class="required"'; ?>>Name</label>
					<input name="author" id="author" type="text" tabindex="3" <?php if ( $req ) echo "aria-required='true'"; ?> />

				<!--END #form-section-author-->
				</div>

				<!--BEGIN #form-section-email-->
				<div id="form-section-email" class="form-section">
					<label for="email"<?php if ( $req ) echo ' class="required"'; ?>>Email</label>
					<input name="email" id="email" type="text" tabindex="4" <?php if ( $req ) echo "aria-required='true'"; ?> />
				<!--END #form-section-email-->
				</div>

				<!--BEGIN #form-section-actions-->
				<div id="form-section-actions" class="form-section">
					<button name="submit" id="submit" type="submit" tabindex="5">Post your comment</button>
					<?php comment_id_fields(); ?>
				<!--END #form-section-actions-->
				</div>

			<?php do_action( 'comment_form', $post->ID ); // Available action: comment_form ?>
			<!--END #comment-form-->
			</form>

			<?php endif; // If registration required and not logged in ?>
		<!--END #respond-->
		</div>
		<?php
	}

	return ob_get_clean();
}
add_shortcode('comment-form', 'sc_comment_form');


/**
 * Output Upcoming Events via shortcode.
 **/
function sc_events_widget() {

	ob_start();
	?>
	<div class="events-wrapper">
	<?php
	display_events();

	$options  = get_option( THEME_OPTIONS_NAME );

	// Assuming that the url will not end in a slash so
	// we can append the the feed types correctly
	$base_url = rtrim($options['events_url'], '/') . '/';

	$json_url = $bas_url . 'feed.json';
	$ics_url  = $base_url . 'feed.ics';
	$rss_url  = $base_url . 'feed.rss';

	?>
	<p class="screen-only"><a href="<?php echo $base_url; ?>" class="events_morelink">More Events</a></p>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('events-widget', 'sc_events_widget');


function sc_social_share_buttons() {
	global $post;
	$url = get_permalink( $post->ID );
	$title = $post->post_title;

	return display_social( $url, $title );
}

add_shortcode( 'social-share-buttons', 'sc_social_share_buttons' );


/**
 * Google Remarketing Shortcode
 **/
function sc_google_remarketing( $attr, $content='' ) {
	$conversion_id = isset( $attr['conversion_id'] ) ? $attr['conversion_id'] : '';
	$img_src = isset( $attr['img_src'] ) ? $attr['img_src'] : '';

	if ( $conversion_id && $img_src ) {
		ob_start();

		?>
		<script type="text/javascript">
			// <![CDATA[
			var google_conversion_id = <?php echo $conversion_id; ?>;
			var google_custom_params = window.google_tag_params;
			var google_remarketing_only = true;
			// ]]>
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
		<noscript>
			<div style="display:inline">
				<img height="1" width="1" style="border-style:none;" alt="" src="<?php echo $img_src; ?>">
			</div>
		</noscript>

		<?php

		return ob_get_clean();

	} else {
		return '';
	}
}
add_shortcode( 'google-remarketing', 'sc_google_remarketing' );


/**
 * Facebook Audience Tracking
 **/
function sc_facebook_tracking( $attr, $content='' ) {
	$audience_id = isset( $attr['audience_id'] ) ? $attr['audience_id'] : '';
	$img_src = isset( $attr['img_src'] ) ? $attr['img_src'] : '';

	if ( $audience_id && $img_src ) {
		ob_start();

	?>

	<script type="text/javascript">
		(function() {
			var _fbq = window._fbq || (window._fbq = []);

			if (!_fbq.loaded) {
				var fbds = document.createElement('script');
				fbds.async = true;
				fbds.src = '//connect.facebook.net/en_US/fbds.js';
				var s = document.getElementByTagName('script')[0];
				s.parentNode.insertBefore(fbds, s);
				_fbq.loaded = true;
			}

			_fbq.push(['addPixelId', '<?php echo $audience_id; ?>']);
		})();

		window._fbq = window._fbq || [];
		window._fbq.push(['track', 'PixelInitialized', {}]);
	</script>
	<noscript>
		<img height="1" width="1" alt="" style="display:none;" src="<?php echo $img_src; ?>">
	</noscript>
	<?php

	} else {
		return '';
	}
}
add_shortcode( 'facebook-tracking', 'sc_facebook_tracking' );


/**
 * Wrap arbitrary text in .lead paragraph.  Only handles single lines of text.
 **/
function sc_lead( $attr, $content='' ) {
	return '<p class="lead">' . $content . '</p>';
}
add_shortcode( 'lead', 'sc_lead' );

/**
 * Insert tubular JS to add background video
 **/
function sc_tubular( $attr ) {
	$id = $attr['id'] ? $attr['id'] : null;
	ob_start();
	?>
	<script type="text/javascript" charset="utf-8" src="//cdn.lukej.me/jquery.tubular/1.0.1/jquery.tubular.1.0.js"></script>
	<script type="text/javascript">
		$(function() {
			$('main').tubular({videoId: '<?php echo $id; ?>'});
		});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tubular', 'sc_tubular' );

?>
