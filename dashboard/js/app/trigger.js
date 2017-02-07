$(document).ready(function() {
	var $trigger = $('.sc-trigger'),
		$triggered = undefined;

	$trigger.click(function() {
		var $this = $(this),
			$element = $('#'+$this.data('sc-trigger'));

		$triggered = $element;

		if( $element.hasClass('sc-expanded') ) {
			$element.removeClass('sc-expanded');
			$(document).trigger('collapsed');
		} else {
			$element.addClass('sc-expanded');
			$(document).trigger('expanded');
		}
	});

	$(document).mouseup(function (e) {
		if( $triggered !== undefined ) {
			if( $triggered.visible() ) {
				// if the target of the click isn't the $triggered nor a descendant of the $triggered
				if( !$triggered.is( e.target ) && $triggered.has( e.target ).length === 0 ) {
					$triggered.removeClass( 'sc-expanded' );
					$(document).trigger('collapsed');
				}
			}
		}
	});

	// Add element behind title in appbar to place other elements at the end
	$('.sc-appbar-title').after('<div class="sc-appbar-spacer"></div>');
});
