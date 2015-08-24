	<footer class="site-footer">
		<?php echo display_profile_list(); ?>
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-push-4 col-md-7 col-md-push-5 col-sm-12">
					<div class="footer-headline-address-wrap">
						<aside class="footer-section footer-address">
							<!-- TODO: where will this content be pulled from? -->
							<h2 class="footer-section-heading">Impact.</h2>
							<address>
								4000 Central Florida Blvd.<br>
								Orlando, Florida, 32816<br>
								<a href="tel:4078232000">407.823.2000</a>
							</address>
							<a href="http://www.ucf.edu/">www.ucf.edu</a>
							<div class="divider-squares"></div>
						</aside>
						<aside class="footer-section footer-headlines">
							<!-- TODO: where will this content be pulled from? -->
							<h2 class="footer-section-heading">Headlines</h2>
							<p>
								A short explanation of UCF that will get the reader interested and wanting to read the story that is about twentyfive words long.
							</p>
							<img class="ucf-logo-white" src="<?php echo THEME_IMG_URL; ?>/logo.png" alt="UCF logo" title="UCF logo"><!-- TODO -->
						</aside>
					</div>
				</div>
				<div class="col-lg-4 col-lg-pull-8 col-md-5 col-md-pull-7 col-sm-12 col-xs-nopad">
					<aside class="footer-section give-section-white">
						<!-- TODO: where will this content be pulled from? -->
						<h2 class="give-section-heading">What Will Your Impact Be?</h2>
						<p>Help UCF students continue to make an impact on our community today.</p>
						<a class="btn btn-primary btn-xl btn-block" href="#">Give Now</a>
					</aside>
				</div>
			</div>
		</div>
	</footer>

	</body>
	<?php echo "\n".footer_()."\n"; ?>
</html>
