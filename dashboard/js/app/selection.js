$(document).ready(function() {
	var $selection = $('.sc-checkbox, .sc-radio');

	$selection.each(function() {
		var $this = $(this),
			$id = $this.attr('id'),
			$lever = $this.closest('.sc-switch').length;

		if( $lever == 0 ) {
			$this.after( '<label for="' + $id + '"></label>' );
		}
	});
});