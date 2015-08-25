		<footer class="site-footer">
			<?php echo display_profile_list(); ?>
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-xs-nopad">

						<div class="footer-column-wrap">

							<div class="footer-give-column-wrap">
								<aside class="footer-section give-section-white">
									<?php echo display_footer_cta(); ?>
								</aside>
							</div>

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
								$col3_uselogo = get_theme_option( 'footer_col3_logo' );

								if ( $col3_content || $col3_uselogo ):
								?>
								<aside class="footer-section footer-customcol">
									<?php if ( $col3_title ): ?>
									<h2 class="footer-section-heading"><?php echo wptexturize( $col3_title ); ?></h2>
									<?php endif; ?>

									<?php echo apply_filters( 'the_content', $col3_content ); ?>

									<?php if ( $col3_uselogo ): ?>
									<img class="ucf-logo-white" src="<?php echo THEME_IMG_URL; ?>/logo.png" alt="UCF logo" title="UCF logo">
									<?php endif; ?>
								</aside>
								<?php endif; ?>
							</div>

							<div class="footer-give-fullwidth-wrap">
								<aside class="footer-section give-section-white">
									<?php echo display_footer_cta(); ?>
								</aside>
							</div>

						</div>

					</div>
				</div>
			</div>
		</footer>
	</body>
	<?php echo footer_(); ?>
</html>
