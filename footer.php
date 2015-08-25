		<footer class="site-footer">
			<?php echo display_profile_list(); ?>
			<div class="container">
				<div class="row">
					<div class="col-lg-8 col-lg-push-4 col-md-7 col-md-push-5 col-sm-12">
						<div class="footer-address-custom-wrap">
							<aside class="footer-section footer-address">
								<?php
								$org_name = get_theme_option( 'organization_name' );
								if ( $org_name ):
								?>
								<h2 class="footer-section-heading"><?php echo wptexturize( $org_name ); ?></h2>
								<?php endif; ?>

								<?php echo display_address(); ?>

								<a href="http://www.ucf.edu/">www.ucf.edu</a>

								<?php echo wp_nav_menu( array(
									'theme_location' => 'social-links',
									'container' => false,
									'menu_class' => 'social-links',
									'depth' => 1,
								) );
								?>
							</aside>

							<?php
							$col3_title = get_theme_option( 'footer_col3_heading' );
							$col3_content = get_theme_option( 'footer_col3_content' );

							if ( $col3_content ):
							?>
							<aside class="footer-section footer-customcol">
								<?php if ( $col3_title ): ?>
								<h2 class="footer-section-heading"><?php echo wptexturize( $col3_title ); ?></h2>
								<?php endif; ?>

								<?php echo apply_filters( 'the_content', $col3_content ); ?>

								<img class="ucf-logo-white" src="<?php echo THEME_IMG_URL; ?>/logo.png" alt="UCF logo" title="UCF logo">
							</aside>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-lg-4 col-lg-pull-8 col-md-5 col-md-pull-7 col-sm-12 col-xs-nopad">
						<aside class="footer-section give-section-white">
							<?php
							$cta_title = get_theme_option( 'footer_cta_title' );
							$cta_desc = get_theme_option( 'footer_cta_description' );
							$cta_text = get_theme_option( 'footer_cta_text' );
							$cta_url = get_theme_option( 'footer_cta_url' );
							?>

							<?php if ( $cta_title ): ?>
							<h2 class="give-section-heading"><?php echo wptexturize( $cta_title ); ?></h2>
							<?php endif; ?>

							<?php if ( $cta_desc ): ?>
							<?php echo apply_filters( 'the_content', $cta_desc ); ?>
							<?php endif; ?>

							<?php if ( $cta_text && $cta_url ): ?>
							<a class="btn btn-primary btn-xl btn-block" href="<?php echo $cta_url; ?>">
								<?php echo wptexturize( $cta_text ); ?>
							</a>
							<?php endif; ?>
						</aside>
					</div>
				</div>
			</div>
		</footer>
	</body>
	<?php echo "\n" . footer_() . "\n"; ?>
</html>
