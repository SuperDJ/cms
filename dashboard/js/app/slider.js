$(document).ready(function() {
	var $range = $('input[type="range"].sc-slider');

	// Add range inside div
	$range.each(function() {
		var $this = $(this),
			$slider;

		if( $this.hasClass('sc-slider-discrete') ) {
			$slider = '<div class="sc-range"><div class="sc-slider-bubble"></div>' + $this.getHTML() + '<div class="sc-range-track"><div class="sc-range-track-before"></div><div class="sc-range-track-after"></div></div></div>';
		} else {
			$slider = '<div class="sc-range">' + $this.getHTML() + '<div class="sc-range-track"><div class="sc-range-track-before"></div><div class="sc-range-track-after"></div></div></div>';
		}
		// Add html after range slider
		$this.replaceWith( $slider );
	});

	// Calculate
	$range = $('.sc-range .sc-slider');
	$range.on('input', function() {
		var $this = $(this),
			$minVal = ( $this.attr('min') != undefined ? $this.attr('min') : 0 ),
			$maxVal = ( $this.attr('max') != undefined ? $this.attr('max') : 100 ),
			$percentage = ( $this.val() / ( $maxVal - $minVal ) ) * 100, // Calculate percentage
			$before = $this.closest('div').find('.sc-range-track-before'),
			$after = $this.closest('div').find('.sc-range-track-after');

		$before.css('width', $percentage+'%');
		$after.css('width', (100 - $percentage)+'%');

		// If range slider is discrete add value in bubble
		if( $this.hasClass('sc-slider-discrete') ) {
			var $bubble = $this.parent('div').find('.sc-slider-bubble');
			// Set value in bubble
			$bubble.text($this.val());
			$bubble.css('left', $percentage+'%');

			// If thumb is over 0%
			if( $percentage > 0 ) {
				$bubble.addClass('sc-not-null');
			} else {
				$bubble.removeClass('sc-not-null');
			}
		}

		// If thumb is over 0%
		if( $percentage > 0 ) {
			$this.addClass('sc-not-null');
		} else {
			$this.removeClass('sc-not-null');
		}

		// If slider is disabled
		if( $this.hasClass('sc-disabled') || $this.attr('disabled') == 'disabled' ) {
			$this.parent('div').addClass('sc-disabled');
		}
	});
	$range.trigger('input');
});