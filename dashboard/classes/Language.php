<?php
class Language extends Database {
	private $_current; // Store current language

	public 	$languages, // Store all languages data
			$translation; // Store translation of language

	function __construct( $language ) {
		parent::__construct();

		$this->_current = $language;

		$this->languages = $this->data()['languages'];
	}

	public function translate( $word ) {
		if( empty( $word ) ) {
			return false;
		} else {
			return $word;
		}
	}

	private function addTranslation( $word ) {

	}

	public function editTranslation( array $data, $id ) {

	}

	public function addLanguage( array $data ) {
		$user = base64_decode( $_SESSION['user'] );

		$stmt = $this->mysqli->prepare("INSERT INTO `languages` (`language`, `iso_code`, `edited_by`) VALUES (?, ?, ?)");
		$stmt->bind_param("ssi", $data['language'], $data['iso_code'], $user);
		$stmt->execute();

		if( $stmt->affected_rows >= 1 ) {
			return true;
		} else {
			return false;
		}
	}

	public function editLanguage( array $data, $id ) {
		if( empty( $id ) ) {
			return false;
		} else {
			$stmt = $this->mysqli->prepare("UPDATE `languages` SET `language` = ?, `iso_code` = ?, `edited_by` = ? WHERE `id` = ?");
			$stmt->bind_param('ssii', $data['language'], $data['iso_code'], $data['user'], $id);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function deleteLanguage( $id ) {
		if( empty( $id ) ) {
			return false;
		} else {
			$stmt = $this->mysqli->prepare("DELETE FROM `languages` WHERE `id` = ?");
			$stmt->bind_param('i', $id);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Get all languages and their data
	 *
	 * @param $id
	 *
	 * @return array|bool
	 */
	public function data( $id = null ) {
		/*if( is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("SELECT `id`, `language`, `iso_code`, `edited_by` FROM `languages`");
		} else {
			$stmt = $this->mysqli->prepare("SELECT `id`, `language`, `iso_code`, `edited_by` FROM `languages` WHERE `id` = ?");
			$stmt->bind_param('i', $id);
		}
		$stmt->execute();

		if( $stmt->num_rows >= 1 ) {
			$data = array();
			while( $row = $stmt->fetch_assoc() ) {
				$data[] = $row;
			}

			$stmt->close();
			if( !empty( $data ) ) {
				return $data;
			}  else {
				return false;
			}
		} else {
			return false;
		}*/
	}
}