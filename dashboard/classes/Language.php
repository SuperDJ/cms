<?php
class Language extends Database implements Plugin {
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

	public function add( array $data ) {
		$stmt = $this->mysqli->prepare("INSERT INTO `languages` (`language`, `iso_code`) VALUES (:language, :iso_code)");
		$stmt->bindParam(':language', $data['language'], PDO::PARAM_STR);
		$stmt->bindParam(':iso_code', $data['iso_code'], PDO::PARAM_STR);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function edit( array $data ) {
		$stmt = $this->mysqli->prepare("UPDATE `languages` SET `language` = :language, `iso_code` = :iso_code WHERE `id` = :id");
		$stmt->bindParam(':language', $data['language'], PDO::PARAM_STR);
		$stmt->bindParam(':iso_code', $data['iso_code'], PDO::PARAM_STR);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `languages` WHERE `id` = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("
				SELECT `l`.`id`, `l`.`language`, `l`.`iso_code`, concat( round( 100 * count(`t`.`languages_id`) / `t2`.`cnt`, 0 ), '%') AS `translated`
				FROM `languages` `l`
				  	LEFT JOIN `translations` `t`
						ON `l`.`id` = `t`.`languages_id`
				  	CROSS JOIN (
						SELECT count(`id`) `cnt`
						FROM `translations`
						WHERE `languages_id` = 1
					) `t2`
				WHERE `l`.`id` = :id	 
				GROUP BY `l`.`id`");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("
				SELECT `l`.`id`, `l`.`language`, `l`.`iso_code`, concat( round( 100 * count(`t`.`languages_id`) / `t2`.`cnt`, 0 ), '%') AS `translated`
				FROM `languages` `l`
				  	LEFT JOIN `translations` `t`
						ON `l`.`id` = `t`.`languages_id`
				  	CROSS JOIN (
						SELECT count(`id`) `cnt`
						FROM `translations`
						WHERE `languages_id` = 1
					) `t2`	 
				GROUP BY `l`.`id`");
		}
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = null;
			return $result;
		} else {
			$stmt = null;
			return false;
		}
	}
}