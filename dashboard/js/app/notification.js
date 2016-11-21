function notification( $title, $options, $click ) {
	// Check browser support
	if( !('Notification' in window) ) {
		alert( 'Your browser doesn\'t support notifications' );
	} else if( Notification.permission === 'granted' ) {
		var $notification = new Notification( $title, $options );
		$notification.onclick = function () {
			window.open( $click, '_blank' );
		}
	} else if( Notification.permission === 'denied' ) { // If the user doesn't allows notifications
		// Ask user again
		Notification.requestPermission( function ( $permission ) {
			if( $permission === 'granted' ) {
				new Notification( 'You can now receive notifications' );
			}
		});
	}
}

$('.sc-notification-header-expand').click(function() {
	var $this = $(this).closest('.sc-notification');

	if( $this.hasClass('sc-expanded') ) {
		$this.removeClass('sc-expanded');
	} else {
		$this.addClass('sc-expanded');
	}
});