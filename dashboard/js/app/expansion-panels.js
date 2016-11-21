$(document).ready(function() {
	var $expansionPanel = $('.sc-expansion-panel');

	$expansionPanel.each(function() {
		var $this = $(this),
			$li = $this.find('li');

		// Add arrow
		$li.each(function() {
			var $this = $(this),
				$content = $this.find('.sc-expansion-panel-header').html();

			$this.find('.sc-expansion-panel-header').html($content + '<span class="sc-expand-icon"><i class="material-icons">expand_more</i></span>');

			var $expand = $this.find('.sc-expand-icon');
			$expand.click(function() {
				if( $this.hasClass('sc-expanded') ) {
					$this.removeClass('sc-expanded');
				} else {
					$this.addClass('sc-expanded');
				}
			});
		});
	});
});