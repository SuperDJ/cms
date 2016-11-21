$(document).ready(function() {
	if( $('#password') != undefined ) {
		var $pw = $('#password'),
			$pwAgain = $('#password_again'),
			$pwEnc = $('#password_encrypted'),
			$form = $('form');

		$pw.keyup(function() {
			var $wordArray = CryptoJS.enc.Utf8.parse($pw.val()),
				$base64 = CryptoJS.enc.Base64.stringify($wordArray);

			$pwEnc.val($base64);
			console.log('pwe1: '+$pwEnc.val());
		});

		$form.submit(function(e) {
			e.preventDefault();
			var $base64 = $pwEnc.val();

			$pw.val($base64);
			$pwAgain.val($base64);
			$pwEnc.val(CryptoJS.MD5($base64));

			console.log('pw: '+$pw.val());
			console.log('pwa: '+$pwAgain.val());
			console.log('pwe2:'+$pwEnc.val());

			$form.unbind().submit();
		});
	}
});