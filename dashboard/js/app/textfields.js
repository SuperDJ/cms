$(document).ready(function() {
	var $input = $('input, textarea');

	// If page is loaded and input has a value
	$input.each(function() {
		var $this = $(this),
			$value = $this.val(),
			$label = $this.parent('div').find('label');

		if( !empty($value) ) {
			$label.addClass('sc-active');
		} else {
			$label.removeClass('sc-active');
		}
	});

	// When typing
	$input.keyup(function() {
		var $this = $(this),
			$value = $this.val(),
			$label = $this.parent('div').find('label');

		if( !empty($value) ) {
			$label.addClass('sc-active');
		} else {
			$label.removeClass('sc-active');
		}
	});

	// Fix bug that text field isn't selected when label is clicked
	$('label').click(function() {
		var $id = $(this).attr('for');

		if( $id != undefined ) {
			if( $( '#' + $id ).is( ':checkbox, :radio' ) ) {
				return;
			}

			$( '#' + $id ).trigger( 'click' );
			/*if( $( '#' + $id ).attr('input') == 'checkbox' || $( '#' + $id ).attr('input') == 'radio' ) {
				return;
			} else {
				$( '#' + $id ).trigger( 'click' );
			}*/
		}
	});

	'use strict';

	;( function( $, window, document, undefined )
	{
		$( '.sc-file-input' ).each( function()
		{
			var $input	 = $( this ),
				$label	 = $input.next( 'label' ),
				labelVal = $label.html();

			$input.on( 'change', function( e )
			{
				var fileName = '';

				if( this.files && this.files.length > 1 )
					fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
				else if( e.target.value )
					fileName = e.target.value.split( '\\' ).pop();

				if( fileName )
					$label.find( 'span' ).html( fileName );
				else
					$label.html( labelVal );
			});

			// Firefox bug fix
			$input
				.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
				.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
		});
	})( jQuery, window, document );
});