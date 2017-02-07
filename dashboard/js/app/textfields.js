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
});