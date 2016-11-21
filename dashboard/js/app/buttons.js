$(document).ready(function() {
	$('.sc-toggle-button').each(function() {
		var $this = $(this),
			$icon = $this.data('sc-toggle-icon');

		$this.hide().after('<div class="sc-toggle-button"><i class="material-icons">' + $icon + '</i></div>');

		$this.click(function() {
			$this.toggleClass('checked').prev().prop('checked', $(this).is('.checked'))
		});
	});
});