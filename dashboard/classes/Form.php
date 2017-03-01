<?php
class Form extends Database {
	public 	$errors = array(), // Storing all errors
			$return = array(), // Storing all fields and value to be used in database
			$remember = array(), // Storing all fields and value to be used in form
			$files = array(
				'documents' => array(
					'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'text/x-pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/octet-stream', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'application/vnd.ms-word.document.macroEnabled.12', 'application/vnd.ms-word.template.macroEnabled.12', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'application/vnd.ms-excel.sheet.macroEnabled.12', 'application/vnd.ms-excel.template.macroEnabled.12', 'application/vnd.ms-excel.addin.macroEnabled.12', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.presentationml.template', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
				),
				'images' => array(
					'image/cgm', 'image/g3fax', 'image/gif', 'image/ief', 'image/jpeg', 'image/naplps', 'image/pcx', 'image/png', 'image/prs.btif', 'image/prs.pti', 'image/svg+xml', 'image/tiff', 'image/vnd.cns.inf2', 'image/vnd.djvu', 'image/vnd.dwg', 'image/vnd.dxf', 'image/vnd.fastbidsheet', 'image/vnd.fpx', 'image/vnd.fst', 'image/vnd.fujixerox.edmics-mmr', 'image/vnd.fujixerox.edmics-rlc', 'image/vnd.mix', 'image/vnd.net-fpx', 'image/vnd.svf', 'image/vnd.wap.wbmp', 'image/vnd.xiff', 'image/x-cmu-raster', 'image/x-coreldraw', 'image/x-coreldrawpattern', 'image/x-coreldrawtemplate', 'image/x-corelphotopaint', 'image/x-icon', 'image/x-jg', 'image/x-jng', 'image/x-ms-bmp', 'image/x-photoshop', 'image/x-portable-anymap', 'image/x-portable-bitmap', 'image/x-portable-graymap', 'image/x-portable-pixmap', 'image/x-rgb', 'image/x-xbitmap', 'image/x-xpixmap', 'image/x-xwindowdump'
				),
				'videos' => array(
					'video/dl', 'video/fli', 'video/gl', 'video/mpeg', 'video/mp4', 'video/quicktime', 'video/mp4v-es', 'video/parityfec', 'video/pointer', 'video/vnd.fvt', 'video/vnd.motorola.video', 'video/vnd.motorola.videop', 'video/vnd.mpegurl', 'video/vnd.mts', 'video/vnd.nokia.interleaved-multimedia', 'video/vnd.vivo', 'video/x-dv', 'video/x-la-asf', 'video/x-mng', 'video/x-ms-asf', 'video/x-ms-wm', 'video/x-ms-wmv', 'video/x-ms-wmx', 'video/x-ms-wvx', 'video/x-msvideo', 'video/x-sgi-movie'
				),
				'tracks' => array(
					'audio/aiff', 'audio/x-aiff', 'audio/mpeg', 'audio/x-realaudio', 'audio/wav', 'audio/ogg', 'audio/midi', 'audio/x-ms-wma', 'audio/x-ms-wax', 'audio/x-matroska', 'audio/x-aac', 'audio/adpcm', 'audio/basic', 'audio/x-caf', 'audio/vnd.dra', 'audio/vnd.dts', 'audio/vnd.dts.hd', 'audio/vnd.nuera.ecelp4800', 'audio/vnd.nuera.ecelp7470', 'audio/vnd.nuera.ecelp9600', 'audio/vnd.digital-winds', 'audio/flac', 'audio/vnd.lucent.voice', 'audio/x-mpegurl', 'audio/mp4', 'audio/vnd.ms-playready.media.pya', 'audio/x-pn-realaudio', 'audio/vnd.rip', 'audio/x-pn-realaudio-plugin', 'audio/s3m', 'application/vnd.yamaha.smaf-audio', 'audio/silk', 'audio/vnd.dece.audio', 'audio/x-wav', 'audio/webm', 'audio/xm', 'audio/mpeg3', 'audio/x-mpeg-3', 'audio/mp3'
				)
			); // TODO add allowed file types for tracks

	private $_page; // Store current page

	function __construct() {
		parent::__construct();

		// Delete form session to prevent errors in other forms
		$_SESSION['page'] = $_SERVER['PHP_SELF'];

		if( $this->_page !== $_SESSION['page'] ) {
			unset( $_SESSION['form'] );
		}
		$this->_page = $_SESSION['page'];
	}

	/**
	 * Validate user input
	 *
	 * @param array    $source
	 * @param  array   $items All form fields
	 * @param callable $translate Translate function
	 * @param   null   $id    When updating
	 * @param bool     $html  True or false depending if you want to allow html input
	 *
	 * @return array|bool array          Return save data in array or error messages
	 * @internal param $ type  $source $_POST or $_GET
	 * @internal param $ type  $id     (Optional) Used to check field value with value from database
	 */
	public function check( array $source, array $items, callable $translate, $id = null, bool $html = false ) {
		if( !is_array( $items ) ) {
			return false;
		}

		foreach( $items as $item => $rules ) {
			foreach ( $rules as $rule => $rule_value ) {
				if( isset( $source[$item] ) ) {
					// Remove malicious characters
					if( !empty( $source[$item] ) ) {
						$value = $this->sanitize( $source[$item], $html );
					} else {
						$value = '';
					}
				} else {
					$value = '';
				}

				switch( $rule ) {
					// Check of empty value
					case 'required':
						if( empty( $value ) ) {
							$this->addError($translate( $rules['name'] ).' '.$translate('is empty'));
						}
						break;
					// Validate email and make sure its in lower case
					case 'email':
						if( !filter_var( $value, FILTER_VALIDATE_EMAIL ) && !preg_match( '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^', $value ) ) {
							$this->addError($value.' '.$translate('is not a valid email'));
						} else {
							$source[$item] = strtolower( $value );
						}
						break;
					// Validate for numeric value
					case 'numeric':
						if( !is_numeric( $value ) ) {
							$this->addError($translate( $rules['name'] ).' '.$translate('has to be a number'));
						}
						break;
					// Maximum length
					case 'maxLength':
						if( mb_strlen( $value) > $rule_value ) {
							$this->addError($translate( $rules['name'] ).' '.$translate('has a maximum of').' '.$rule_value.' '.$translate('characters'));
						}
						break;
					// Minimal length
					case 'minLength':
						if( !empty( $rules['required'] ) && $rules['required'] == true ) {
							if( mb_strlen( $value ) < $rule_value ) {
								$this->addError($translate( $rules['name'] ).' '.$translate('has a minimum of').' '.$rule_value.' '.$translate('characters'));
							}
						}
						break;
					// Unique in database
					// For example used to check if a username, email etc is changed for a user
					case 'unique':
						if( !is_null( $id ) && !empty( $value ) ) {
							// Get the current value
							$current_value = $this->detail($item, $rule_value, 'id', $id);
							// Check if the current value is not equal to the the value
							if( $current_value != $value ) {
								// Check if the value is unique in the database
								if( $this->exists($item, $rule_value, $item, $value) ) {
									$this->addError($translate( $rules['name'] ).' '.$value.' '.$translate('already exists'));
								}
							}
						} else {
							if( $this->exists($item, $rule_value, $item, $value) && !empty( $value ) ) {
								$this->addError($translate( $rules['name'] ).' '.$value.' '.$translate('already exists'));
							}
						}
						break;
					// Check against other value
					case 'matches':
						if( $value != $source[$rule_value] ) {
							$this->addError($translate( $rules['name'] ).' '.$translate('does not match').' '.$rule_value);
						}
						break;
					// Check if something already exists
					case 'exists':
						// If $value is numeric most likely it's an id
						if( is_numeric( $value ) ) {
							if( !$this->exists( $item, $rule_value, 'id', $value) ) {
								$this->addError( $translate( $rules['name'] ).' '.$value.' '.$translate( 'does not exists' ) );
							}
						} else {
							if( !$this->exists( $item, $rule_value, $item, $value ) ) {
								$this->addError( $translate( $rules['name'] ).' '.$value.' '.$translate( 'does not exists' ) );
							}
						}
						break;
					// base64 encode
					case 'base64':
						$source[$item] = base64_endcode( $value );
						break;
					// base64 decode
					case 'base64_decode':
						$source[$item] = base64_decode( $value );
						break;
					// md5
					case 'md5':
						$source[$item] = md5( $value );
						break;
					//sha512
					case 'sha512':
						$source[$item] = hash( 'sha512', $value );
						break;
					// sha1
					case 'sha1':
						$source[$item] = sha1( $value );
						break;
					// Remember entered data
					case 'remember':
						if( !empty( $value ) ) {
							$this->remember[$item] = $value;
						}
						break;
					// Check if a date is actually a date
					case 'date':
						if( !empty( $value ) ) {
							if( !preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value) ) {
								$this->addError($translate( $rules['name'] ).' '.$translate('has no valid date').' '.$value);
							}
						}
						break;
					// Check if time has some sort of correct notation
					case 'time':
						if( !empty( $value ) ) {
							if( !preg_match('/([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?/', $value) ) {
								$this->addError($translate( $rules['name'] ).' '.$translate('has no valid time'));
							}
						}
						break;
					// Field must be empty (to prevent bots from entering text)
					case 'captcha':
						if( !empty( $value ) ) {
							$this->addError($translate('Are you a bot').'?');
						}
					// Capitalize first letter
					case 'capitalize':
						$source[$item] = ucfirst( $value );
						break;
					case 'checkbox':
						if( !empty( $source[$item] ) && $source[$item] == 'on' ) {
							$source[$item] = 1;
						} else {
							$source[$item] = 0;
						}
						break;
				}
				$this->return[$item] = $value;
			}
		}

		if( !is_null( $id ) ) {
			$this->return['id'] = (int)$id;
		}

		if( empty( $this->errors ) ) {
			unset( $_SESSION['form'] ); // Delete session
			return $this->return;
		} else {
			$_SESSION['form'] = $this->remember; // Storing the fields and values
			return false;
		}
	}
	/**
	 * Add error messages to array
	 * @param string $error The error message
	 */
	private function addError( string $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Output all error message in list
	 * @return string Error messages
	 */
	public function outputErrors() {
		$html = '';
		if( !empty( $this->errors ) ) {
			$html .= '	<div class="error sc-card sc-card-supporting" role="error">
							<ul>';
			foreach( $this->errors as $error ) {
				$html .= '		<li>' . $error . '</li>';
			}
			$html .= '		</ul>
						</div>';
		}
		return $html;
	}

	/**
	 * Store input values
	 * When form fails to submit values can be reinserted into form
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	public function input( $field ) {
		if( empty( $field ) ) {
			return false;
		}

		if( !empty( $_SESSION['form'] ) ) {
			$input = $_SESSION['form'];
			if( !empty( $input[$field] ) ) {
				return $input[$field];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Validate media
	 *
	 * @param array    $data All files
	 * @param callable $translate Translate function
	 * @param string   $type (Optional) check if file is of specific type
	 *
	 * @return bool
	 */
	public function media( array $data, callable $translate, string $type = '' ) {
		$totalFiles = count($data);

		$i = 0;
		foreach( $data as $file => $key ) {
			// Check if key file no errors
			if( $key['error'][$i] !== 0 ) {
				switch( $key['error'][$i] ) {
					case 1:
						$this->addError($key['name'][$i].' '.$translate('exceeds the maximum size'));
						break;
					case 2:
						$this->addError($key['name'][$i].' '.$translate('exceeds the maximum size'));
						break;
					case 3:
						$this->addError($key['name'][$i].' '.$translate('was only partially uploaded'));
						break;
					case 4:
						$this->addError($key['name'][$i].' '.$translate('was not uploaded'));
						break;
					case 6:
						$this->addError($translate('Missing a temporary folder'));
						break;
					case 7:
						$this->addError($translate('Failed to write key to disk'));
						break;
				}
			}

			// Check if the file is allowed
			if( empty( array_search_multi($key['type'][$i], $this->files) ) ) {
				$this->addError($key['name'][$i].' '.$translate('has a not allowed file type'));
			}

			// Check if the file is of the right type
			if( !empty( $type ) ) {
				if( $type !== array_search_multi($key['mime'][$i], $this->files) ) {
					$this->addError($key['name'][$i].' '.$translate('is not a').' '.$translate(substr( $type, 0, -1 )));
				}
			}

			// TODO add check to make sure uploads don't exceed max upload

			$i++;
		}

		if( $i == $totalFiles ) {
			return true;
		} else {
			return false;
		}
	}
}