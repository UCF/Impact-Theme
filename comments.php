<?php
/**
 * Template: Comments.php
 */
// Make sure comments.php doesn't get loaded directly
if ( !empty( $_SERVER[ 'SCRIPT_FILENAME' ] ) && 'comments.php' == basename( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) {
	die ( 'Please do not load this page directly. Thanks!' );
}

if ( post_password_required() ) { ?>
	<p class="password-protected alert">This post is password protected. Enter the password to view comments.</p>
<?php return; } ?>

<?php if ( have_comments() ) : // If comments exist for this entry, continue ?>
<!--BEGIN #comments-->
<div id="comments">

<?php
	$comments = get_comments(array(
		'order'  => 'DESC',
		'status' => 'approve',
	));
?>
<?php if ( ! empty( $comments_by_type['comment'] ) ) : ?>
	<!--BEGIN .comment-list-->
	<div class="comment-list">
		<?php
		$is_row_open = False;
		foreach ( $comments as $k => $comment ) :
			if ( $k % 3 == 0 or $k == 0 ) :
				$is_row_open = True;
			?>
				<div class="row">
			<?php
			endif;
		?>
			<div class="span4">
				<div class="comment-item">
					<div class="comment-date">
						<?php comment_date('M j, Y', $comment->comment_ID); ?>
					</div>
					<div class="comment-content">
						<?php comment_text($comment->comment_ID); ?>
					</div>
					<div class="comment-author">
						&mdash; <?php comment_author($comment->comment_ID); ?>
					</div>
				</div>
			</div>
		<?php
			if ($k % 3 == 2) :
				$is_row_open = False;
			?>
				</div>
			<?php
			endif;
		endforeach;

		if ($is_row_open) :
		?>
			</div>
		<?php
		endif;
		?>
	</div>
<?php endif; ?>

<!--END #comments-->
</div>
<?php endif; // ( have_comments() ) ?>
