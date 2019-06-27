<?php

/* Modified for main site theme (for JSON instead of RSS feed): */
function display_events( $start=null, $limit = null ) {
	$options = get_option( THEME_OPTIONS_NAME );

	$start = ( $start ) ? $start : 0;

	// Check for a given limit, then a set Options value, then if none exist, set to 4
	if ( $limit ) {
		$limit = intval($limit);
	} elseif ( $options['events_max_items'] ) {
		$limit = $options['events_max_items'];
	} else {
		$limit = 4;
	}

	$events = get_events( $start, $limit );
	if ( $events !== NULL && count( $events ) ) : ?>
		<table class="events table">
			<tbody class="vcalendar">
				<?php foreach( $events as $item ) :
					$start     = new DateTime($item['starts']);
					$day       = $start->format('M d');
					$time      = $start->format('h:i a');
					$link      = $item['url'];
					$loc_link  = $item['location_url'];
					$location  = $item['location'];
					$title     = $item['title'];
				?>
				<tr class="item vevent">
					<td class="date">
						<div class="day"><?php echo $day; ?></div>
						<div class="dtstart">
							<abbr class="dtstart" title="<?php echo $start->format('c'); ?>"><?php echo $time; ?></abbr>
						</div>
					</td>
					<td class="eventdata">
						<div class="summary"><a href="<?php echo $link; ?>" class="wrap url"><?php echo $title; ?></a></div>
						<div class="location">
							<?php if ( $loc_link ) { ?><a href="<?php echo $loc_link; ?>" class="wrap"><?php } ?><?php echo $location; ?><?php if ( $loc_link ) { ?></a><?php } ?>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p>Events could not be retrieved at this time.  Please try again later.</p>
	<?php endif; ?>
<?php
}


function get_events($start, $limit) {
	$options = get_option( THEME_OPTIONS_NAME );
	// Ensures the last character is not a slash
	$url     = rtrim($options['events_url'], '/') . '/feed.json';

	// Set a timeout
	$args = array(
		'timeout' => FEED_FETCH_TIMEOUT
	);

	// Grab the feed
	$raw_events = wp_remote_retrieve_body( wp_remote_get( $url, $args ) );
	if ( $raw_events ) {
		$events = json_decode($raw_events, TRUE);
		$events = array_slice($events, $start, $limit);
		return $events;
	} else {
		return NULL;
	}
}

?>
