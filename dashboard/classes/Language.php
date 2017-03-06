<?php
class Language extends Database implements Plugin {
	public $languages;

	public $_current, // Store current language
			$_translations = array(); // Store translation of language

	function __construct( $language ) {
		parent::__construct();

		$this->_current = $language;
		$this->languages = array_column( $this->data(), 'id');
		//print_r($this->translations($this->_current));
		$this->_translations = $this->translations($this->_current); // Store translations to improve page load speed
	}

	/**
	 * Translate word
	 *
	 * @param $word string Word to translate
	 *
	 * @return bool|mixed
	 */
	public function translate( $word ) {
		if( empty( $word ) ) {
			return false;
		} else {
			// If word already in database return translation else add word to database
			if( in_array( $word, array_keys( $this->_translations ) ) || $this->exists('translation', 'translations', 'translation', $word) ) {
				if( empty($this->_translations[ $word ] ) ) {
					return $word;
				} else {
					return $this->_translations[$word];
				}
			} else {
				$stmt = $this->mysqli->prepare("INSERT INTO `translations` (`translation`) VALUES (:translation)");
				$stmt->bindParam(':translation', $word, PDO::PARAM_STR);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					return $word;
				} else {
					return false;
				}
			}
		}
	}

	/**
	 * Get all translations from a specific language
	 *
	 * @param $id
	 *
	 * @return array|bool
	 */
	private function translations( $id ) {
		// TODO Make 1 dynamic
		if( $id != 1 ) {
			$stmt = $this->mysqli->prepare( "
				SELECT `d`.`translation` AS `default`, `t`.`translation` FROM `translations` `d`
				CROSS JOIN `translations` `t`
				ON `d`.`id` = `t`.`translations_id`
				WHERE `t`.`languages_id` = :languages_id
			" );
		} else {
			$stmt = $this->mysqli->prepare("SELECT `translation` AS `default`, `translation` FROM `translations` WHERE `languages_id` = :languages_id");
		}
		$stmt->bindParam(':languages_id', $id, PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = null;

			$data = array();
			foreach( $result as $row => $field ) {
				$data[$field['default']] = $field['translation'];
			}

			if( !empty( $data ) ) {
				return $data;
			} else {
				return false;
			}
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Add language to database
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
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

	/**
	 * Edit language in database
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
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

	/**
	 * Delete language from database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
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

	/**
	 * Get all data from languages
	 * TODO make languages_id dynamic
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
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
				GROUP BY `l`.`id`
				LIMIT 1");
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

	/**
	 * Get all data from translations
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function translationData( int $id ) {
		$stmt = $this->mysqli->prepare("SELECT `id`, `translation` FROM `translations`WHERE `languages_id` = :languages_id");
		$stmt->bindParam(':languages_id', $id, PDO::PARAM_INT);
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

	/**
	 * Get translated words from database
	 * Used in translate.php
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function translated( int $id ) {
		// Get all translated fields
		$stmt = $this->mysqli->prepare("SELECT `translation`, `translations_id` FROM `translations` WHERE `languages_id` = :languages_id");
		$stmt->bindParam(':languages_id', $id, PDO::PARAM_INT);
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

	/**
	 * Insert, delete, edit translations
	 * Used in translate.php
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function translation( array $data ) {
		// Get all translated fields
		$result = $this->translated($data['id']);

		$update = array();
		$insert = array();
		foreach( $data as $field => $value ) {
			if( $field !== 'id' ) {
				if( in_array( $field, array_column( $result, 'translations_id' ) ) ) {
					// Get result key
					$resultKey = array_search_multi($field, $result);
					// If submitted value is not equal to current value add $value to update array
					if( $value != $result[$resultKey]['translation'] ) {
						$update[] = array(
							'value' => $value,
							'translations_id' => $field
						);
					}
				} else {
					$insert[] = array(
						'value' => $value,
						'translations_id' => $field
					);
				}
			}
		}

		$q = 0; // Store finished queries

		if( !empty( $update ) ) {
			$stmt = $this->mysqli->prepare("UPDATE `translations` SET `translation` = :translation WHERE `translations_id` = :translations_id AND `languages_id` = :languages_id");

			$count = count($update);
			$i = 0;
			foreach( $update as $key => $value ) {
				$stmt->bindParam(':translation', $value['value'], PDO::PARAM_STR);
				$stmt->bindParam(':translations_id', $value['translations_id'], PDO::PARAM_INT);
				$stmt->bindParam(':languages_id', $data['id'], PDO::PARAM_INT);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$i++;
				} else {
					$stmt = null;
					return false;
				}
			}

			if( $count === $i ) {
				$stmt = null;
				$q++;
			} else {
				$stmt = null;
				return false;
			}
		} else {
			$q++;
		}

		if( !empty( $insert ) ) {
			$stmt = $this->mysqli->prepare("INSERT INTO `translations` (`translation`, `translations_id`, `languages_id`) VALUES (:translation, :translations_id, :languages_id)");

			$count = count($insert);
			$i = 0;
			foreach( $insert as $key => $value ) {
				$stmt->bindParam(':translation', $value['value'], PDO::PARAM_STR);
				$stmt->bindParam(':translations_id', $value['translations_id'], PDO::PARAM_INT);
				$stmt->bindParam(':languages_id', $data['id'], PDO::PARAM_INT);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$i++;
				} else {
					$stmt = null;
					return false;
				}
			}

			if( $count === $i ) {
				$stmt = null;
				$q++;
			} else {
				$stmt = null;
				return false;
			}
		} else {
			$q++;
		}

		if( $q === 2 ) {
			return true;
		} else {
			return false;
		}
	}
}