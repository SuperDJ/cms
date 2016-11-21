$(document).ready(function() {
	$('.sc-chip-delete').click(function() {
		$(this).closest('.sc-chip').remove();
	});
});