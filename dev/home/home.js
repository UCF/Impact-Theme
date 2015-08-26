(function () {
	var resizeTimer,
		$lead = $('#lead'),
		$video = $('#video'),
		$featuredLink = $('.featured-link'),
		documentWidth = $(window).width();

	function resizeLead() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			$lead.css("height", ($(window).height() - 50) + "px");
		}, 250);
	}

	function onResize() {
		// prevent resizing the lead on mobile horizontal scroll
		// http://stackoverflow.com/questions/17328742/mobile-chrome-fires-resize-event-on-scroll
		if (documentWidth !== $(window).width()) {
			documentWidth = $(window).width();
			resizeLead();
		}
	}

	function toggleLinks() {
		if (this.currentTime) {
			$featuredLink.addClass('fade');
			if (this.currentTime > 5) {
				$featuredLink.eq(2).removeClass('fade');
			} else if (this.currentTime > 2.75) {
				$featuredLink.eq(1).removeClass('fade');
			} else if (this.currentTime > 0) {
				$featuredLink.eq(0).removeClass('fade');
			}
		}
	}

	function hideVideoStill() {
		$('#video-image').hide();
		$video.show();
	}

	function showVideoStill() {
		$('#video-image').show();
		$video.hide();
		$featuredLink.removeClass('fade');
	}

	function smoothScroll(e) {
        e.preventDefault();
		$('html, body').animate({
			scrollTop: $($(this).attr('data-target')).offset().top
		}, 500);
	}

	function setupEventHandlers() {
		$(window).on('resize', onResize);
		$video.on('timeupdate', toggleLinks);
		$video.on('play', hideVideoStill);
		$video.on('ended', showVideoStill);
		$('.smooth-scroll').on('click', smoothScroll)
	}

	function init() {
		resizeLead();
		setupEventHandlers();
	}
	$(init);
} ());
