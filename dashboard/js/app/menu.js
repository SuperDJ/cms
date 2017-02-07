$(document).ready(function() {
	if( $('.sc-menu').length >= 1 ) {
		var $menu = $( '.sc-menu' );

		$menu.each( function () {
			var $this = $( this ),
				// Store height and width
				$height = $this.css('height'),
				$width = $this.css('width');

			if( !empty( $height ) && !empty( $width ) ) {
				$this.css( {'max-height': 0, 'max-width': 0} );
			}

			$(document).on('expanded', function() {
				if( $this.hasClass( 'sc-expanded' ) ) {
					$this.css( {'max-height': $height, 'max-width': $width} );
				} else {
					$this.css( {'max-height': 0, 'max-width': 0} );
				}
			});

			$(document).on('collapsed', function() {
				if( $this.hasClass( 'sc-expanded' ) ) {
					$this.css( {'max-height': $height, 'max-width': $width} );
				} else {
					$this.css( {'max-height': 0, 'max-width': 0} );
				}
			});
		} );
	}
});