(function () {
	var $lead = $('#lead'),
		$video = $('#video');

	function resizeLead() {
		$lead.css("height", ($(window).height() - 50) + "px");
	}

	function setupEventHandlers() {
		$(window).resize(function () {
			resizeLead();
		});
		
		$video.on('ended', function () {
			$video.hide();
			$('#videoImage').show();
		});
	}

	function init() {
		resizeLead();
		setupEventHandlers();
	}
	$(init);
} ());