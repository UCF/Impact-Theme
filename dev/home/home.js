(function () {
	var resizeTimer,
		$window,
		$lead,
		$video,
		$videoImage,
		$featuredLink,
		documentWidth;

	function cacheObjects() {
		$window = $(window);
		$lead = $('#lead');
		$video = $('#video');
		$videoImage = $('#video-image');
		$featuredLink = $('.featured-link');
		documentWidth = $window.width();

	}

	function resizeLead() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			$lead.css('height', ($window.height() - 50) + 'px');
		}, 250);
	}

	function onResize() {
		// prevent resizing the lead on mobile horizontal scroll
		// http://stackoverflow.com/questions/17328742/mobile-chrome-fires-resize-event-on-scroll
		if (documentWidth !== $window.width()) {
			setImageVideoSize();
			documentWidth = $window.width();
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

	function showVideoStill() {
		$('#video-image').show();
		$featuredLink.removeClass('fade');
	}

	function smoothScroll(e) {
        e.preventDefault();
		$('html, body').animate({
			scrollTop: $($(this).attr('data-target')).offset().top
		}, 500);
	}

	function setupEventHandlers() {
		$window.on('resize', onResize);
		$video.on('timeupdate', toggleLinks);
		$video.on('ended', function() {
			showVideoStill();
			$video.hide();
		});
		$('.smooth-scroll').on('click', smoothScroll);
	}

	function setImageVideoSize() {
		var windowWidth = $window.width(),
			windowHeight = $window.height(),
			aspectRatio = $videoImage.width() / $videoImage.height();

		// Resize image/video
		if ((windowWidth / windowHeight) < aspectRatio) {
			$video.removeClass().css('height', windowHeight);
			$videoImage.removeClass().css('height', windowHeight);
		} else {
			$video.removeClass().css('width', windowWidth);
			$videoImage.removeClass().css('width', windowWidth);
		}

		// Center image/video
		if (windowWidth < $videoImage.width()) {
			var leftOffset = -(($videoImage.width() - windowWidth) * .5);
			$video.css('left', leftOffset);
			$videoImage.css('left', leftOffset);
		}
		if (windowHeight < $videoImage.height()) {
			var topOffset = -(($videoImage.height() - windowHeight) * .5);
			$video.css('top', topOffset);
			$videoImage.css('top', topOffset);
		}
	}

	// Test if video auto plays
	function isAutoPlay() {

		var mp4 = 'data:video/mp4;base64,AAAAFGZ0eXBNU05WAAACAE1TTlYAAAOUbW9vdgAAAGxtdmhkAAAAAM9ghv7PYIb+AAACWAAACu8AAQAAAQAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAnh0cmFrAAAAXHRraGQAAAAHz2CG/s9ghv4AAAABAAAAAAAACu8AAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAFAAAAA4AAAAAAHgbWRpYQAAACBtZGhkAAAAAM9ghv7PYIb+AAALuAAANq8AAAAAAAAAIWhkbHIAAAAAbWhscnZpZGVBVlMgAAAAAAABAB4AAAABl21pbmYAAAAUdm1oZAAAAAAAAAAAAAAAAAAAACRkaW5mAAAAHGRyZWYAAAAAAAAAAQAAAAx1cmwgAAAAAQAAAVdzdGJsAAAAp3N0c2QAAAAAAAAAAQAAAJdhdmMxAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAFAAOABIAAAASAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGP//AAAAEmNvbHJuY2xjAAEAAQABAAAAL2F2Y0MBTUAz/+EAGGdNQDOadCk/LgIgAAADACAAAAMA0eMGVAEABGjuPIAAAAAYc3R0cwAAAAAAAAABAAAADgAAA+gAAAAUc3RzcwAAAAAAAAABAAAAAQAAABxzdHNjAAAAAAAAAAEAAAABAAAADgAAAAEAAABMc3RzegAAAAAAAAAAAAAADgAAAE8AAAAOAAAADQAAAA0AAAANAAAADQAAAA0AAAANAAAADQAAAA0AAAANAAAADQAAAA4AAAAOAAAAFHN0Y28AAAAAAAAAAQAAA7AAAAA0dXVpZFVTTVQh0k/Ou4hpXPrJx0AAAAAcTVREVAABABIAAAAKVcQAAAAAAAEAAAAAAAAAqHV1aWRVU01UIdJPzruIaVz6ycdAAAAAkE1URFQABAAMAAAAC1XEAAACHAAeAAAABBXHAAEAQQBWAFMAIABNAGUAZABpAGEAAAAqAAAAASoOAAEAZABlAHQAZQBjAHQAXwBhAHUAdABvAHAAbABhAHkAAAAyAAAAA1XEAAEAMgAwADAANQBtAGUALwAwADcALwAwADYAMAA2ACAAMwA6ADUAOgAwAAABA21kYXQAAAAYZ01AM5p0KT8uAiAAAAMAIAAAAwDR4wZUAAAABGjuPIAAAAAnZYiAIAAR//eBLT+oL1eA2Nlb/edvwWZflzEVLlhlXtJvSAEGRA3ZAAAACkGaAQCyJ/8AFBAAAAAJQZoCATP/AOmBAAAACUGaAwGz/wDpgAAAAAlBmgQCM/8A6YEAAAAJQZoFArP/AOmBAAAACUGaBgMz/wDpgQAAAAlBmgcDs/8A6YEAAAAJQZoIBDP/AOmAAAAACUGaCQSz/wDpgAAAAAlBmgoFM/8A6YEAAAAJQZoLBbP/AOmAAAAACkGaDAYyJ/8AFBAAAAAKQZoNBrIv/4cMeQ==',
			body = document.getElementsByTagName('body')[0];;

		var video = document.createElement('video');
		video.src = mp4;
		video.autoplay = true;
		video.volume = 0;
		video.style.visibility = 'hidden';

		body.appendChild(video);

		video.play();

		// triggered if autoplay fails
		var removeVideoTimeout = setTimeout(function () {
			body.removeChild(video);
			$('#video').remove();
			showVideoStill();
		}, 50);

		// triggered if autoplay works
		video.addEventListener('play', function () {
			clearTimeout(removeVideoTimeout);
			body.removeChild(video);
		}, false);
	}

	function init() {
		cacheObjects();
		resizeLead();
		setupEventHandlers();
		setImageVideoSize();
		isAutoPlay();
	}
	$(init);
} ());
