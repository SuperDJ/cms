<?php
class Language extends Database {
	private $_current; // Store current language

	public 	$languages, // Store all languages data
			$translation; // Store translation of language

	function __construct( $language ) {
		parent::__construct();

		$this->_current = $language;

		$this->languages = $this->select("SELECT `language` FROM `languages`");
	}

	public function translate( $word ) {
		if( empty( $word ) ) {
			return false;
		} else {
			// If word already in database return translation else add word to database
			if( $this->exists('translation', 'translations', 'translation', $word) ) {
				// Return translation
				return $word;
			} else {
				if( $this->insert("INSERT INTO `translations` (`translation`) VALUES (?)", array($word)) ) {
					return $word;
				} else {
					return false;
				}
			}
		}
	}

	private function addTranslation( $word ) {

	}
}