$(document).ready(function() {
	var $trigger = $('.sc-trigger'),
		$triggered = undefined,
		$drawer = $('#sc-drawer');

	$trigger.click(function() {
		var $this = $(this),
			$element = $('#'+$this.data('sc-trigger'));

		$triggered = $element;

		if( $element.hasClass('sc-expanded') ) {
			$element.removeClass('sc-expanded');
		} else {
			$element.addClass('sc-expanded');
		}
	});

	$(document).mouseup(function (e) {
		if( $triggered !== undefined ) {
			if( $triggered.visible() ) {
				// if the target of the click isn't the $triggered nor a descendant of the $triggered
				if( !$triggered.is( e.target ) && $triggered.has( e.target ).length === 0 ) {
					$triggered.removeClass( 'sc-expanded' );
				}
			}
		}
	});
});
