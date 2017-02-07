/**
 * Slide an element horizontal
 * @return type description
 */
$.fn.slideHorizontal = function($transition, $speed) {
	$(this).animate({width: 'toggle', transition: $transition}, $speed);
	return $(this);
};

/**
 * Rotate a element
 * @param  int $degrees How many degrees to rotate
 * @param  int $ms      The time in which to rotate
 * @return type         description
 */
$.fn.rotate = function( $degrees, $ms, $effect ) {
	$(this).css({
		'-webkit-transform': 'rotate('+ $degrees +'deg)',
		'-webkit-transition': $ms + 'ms',
		'-webkit-transition-timing-function': $effect,
		'-moz-transform': 'rotate('+ $degrees +'deg)',
		'-moz-transition': $ms + 'ms',
		'-moz-transition-timing-function': $effect,
		'-ms-transform': 'rotate('+ $degrees +'deg)',
		'-ms-transition': $ms + 'ms',
		'-ms-transition-timing-function': $effect,
		'transform': 'rotate('+ $degrees +'deg)',
		'transition': $ms + 'ms',
		'transition-timing-function': $effect
	});
	return $(this);
};

/**
 * Check if element is visible
 * @param  string $e Element
 * @return bool    True or false depending if the element is visible
 */
function visible( $e ) {
	if( $e.is(':visible') ) {
		return true;
	} else {
		return false;
	}
}

$.fn.visible = function() {
	if($(this).is(':visible')) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check if something is empty
 * @param  string $value Input
 * @return boolean        True or false depending if empty or not
 */
function empty( $value ) {
	if( $.trim($value).length === 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * Return HTML of element as string
 *
 * @returns String
 */
$.fn.getHTML = function() {
	var $this = $(this),
		$outerHTML;

	if( typeof $this.outerHTML !== 'undefined' ) {
		$outerHTML = $this.outerHTML;
	} else {
		$outerHTML = $this.wrap('<div>').parent().html();
		$this.unwrap();
	}

	return $outerHTML;
}

