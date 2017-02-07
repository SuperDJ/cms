$(window).on('resize load', function() {
	var $tabs = $('.sc-tabs');

	// Set main height from top
	if( $tabs.length >= 1 ) {
		var $appbarHeight = $('.sc-appbar').css('height').replace('px', ''),
			$tabsHeight = $tabs.css('height').replace('px', ''),
			$main = $('main');

		$main.css( 'top', ( Number( $appbarHeight ) + Number( $tabsHeight ) ) + 'px' );
	}
});