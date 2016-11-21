$(window).on('resize load', function(){
	var $tabsHeight = $('.sc-tabs .sc-tab, .sc-tabs .sc-tab-icon, .sc-tabs .sc-tab-icon-text').css('height');

	if( $tabsHeight !== undefined ) {
		var $appbarHeight = $('.sc-appbar').css('height').replace('px', ''),
			$tabsHeight = $tabsHeight.replace('px', ''),
			$main = $('main');

		$main.css( 'top', ( Number( $appbarHeight ) + Number( $tabsHeight ) ) + 'px' );
	}
});