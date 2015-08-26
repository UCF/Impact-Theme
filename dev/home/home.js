(function () {
	var resizeTimer,
		$lead = $('#lead'),
		$video = $('#video'),
		$featuredLink = $('.featured-link');

	function resizeLead() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			$lead.css("height", ($(window).height() - 50) + "px");
		}, 250);
	}

	function toggleLinks() {
		if (this.currentTime) {
			$featuredLink.addClass('fade');
			if (this.currentTime > 6) {
				$featuredLink.eq(2).removeClass('fade');
			} else if (this.currentTime > 3) {
				$featuredLink.eq(1).removeClass('fade');
			} else if (this.currentTime > 0) {
				$featuredLink.eq(0).removeClass('fade');
			}
		}
	}

	function showVideoStill() {
		$('#video-image').show();
		$video.hide();
		$featuredLink.removeClass('fade');
	}

	function setupEventHandlers() {
		$(window).resize(resizeLead);
		$video.on('timeupdate', toggleLinks);
		$video.on('ended', showVideoStill);
	}

	function init() {
		resizeLead();
		setupEventHandlers();
	}
	$(init);
} ());