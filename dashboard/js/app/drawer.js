$(document).ready(function() {
	$('#sc-drawer').find('.sc-active').closest('ul').closest('li').addClass('sc-expanded');

	$('.sc-drawer-dropdown').click(function() {
		var $this = $(this);

		if($this.hasClass( 'sc-expanded' ) ) {
			$this.removeClass( 'sc-expanded' );
		} else {
			$this.addClass( 'sc-expanded' );
		}
	});

	/*$drawer.find('li.sc-drawer-dropdown').each(function() {
		var $this = $(this),
			$arrow = $this.find('.material-icons'),
			$sub = $this.find('.sc-drawer-dropdown');

		if( $sub !== undefined ) {
			$sub.removeClass('sc-expanded');
		}

		if( $arrow !== undefined ) {
			$arrow.addClass('sc-arrow');
		}

		$this.click(function() {
			if( $this.hasClass('sc-expanded') ) {
				$this.removeClass('sc-expanded');
			} else {
				$this.addClass('sc-expanded');
			}
		});
	});*/

	/*$('#sc-nav-button').click(function() {
		var $trigger = $(this).data('sc-trigger'),
			$drawer = $('#' + $trigger);

		if( $drawer.hasClass('sc-expanded') ) {
			$drawer.removeClass('sc-expanded');
		} else {
			$drawer.addClass('sc-expanded');
		}
	});*/

	/*// Close drawer on outside click
	$(document).mouseup(function (e) {
		var $drawerContainer = $('.sc-drawer-container');
		if( $drawerContainer.visible() ) {
			// if the target of the click isn't the $drawer nor a descendant of the $drawer
			if( !$drawer.is(e.target) && $drawer.has(e.target).length === 0 ) {
				$drawer.removeClass('sc-expanded');
			}
		}
	});*/
});