// Encrypt password to avoid "man in the middle"
function encrypt() {
	var $pw = document.getElementById('password'),
		$pwA = document.getElementById('password_again'),
		$pwEnc = CryptoJS.enc.Base64.stringify( CryptoJS.enc.Utf8.parse( $pw.value ) ),
		$pwAenc = ( $pwA != null ? CryptoJS.enc.Base64.stringify( CryptoJS.enc.Utf8.parse( $pwA.value ) ) : '' );

	$pw.value = $pwEnc;
	$pwA.value = ( $pwA != null ? $pwAenc : '' );
}

// Hash the password to store in database
function hash() {
	var $hashing = document.getElementById('password_encrypted'),
		$password = document.getElementById('password').value,

		$hash = CryptoJS.MD5($password);

	$hashing.value = $hash;
}