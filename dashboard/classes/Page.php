<?php
class Page extends Database implements Plugin {

	public function add( array $data ) {
		$date = date('Y-m-d H:i:s');
		$user = $_SESSION['user']['id'];
		$stmt = $this->mysqli->prepare("
			INSERT INTO `pages` (`title`, `content`, `languages_id`, `create_date`, `created_by`, `keywords`) 
			VALUES (:title, :content,  :languages_id, :create_date, :created_by, :keywords)
		");
		$stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
		/*$stmt->bindParam(':sidebars_id', $data['sidebar_id'], PDO::PARAM_INT);*/
		$stmt->bindParam(':languages_id', $data['language'], PDO::PARAM_STR);
		$stmt->bindParam(':create_date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':created_by', $user, PDO::PARAM_INT);
		$stmt->bindParam(':keywords', $data['keywords'], PDO::PARAM_STR);
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
		$date = date('Y-m-d H:i:s');
		$user = $_SESSION['user']['id'];
		$stmt = $this->mysqli->prepare("UPDATE `pages` SET `title` = :title, `content` = :content, `languages_id` = :languages_id, `edit_date` = :edit_date, `edited_by` = :edited_by, `keywords` = :keywords WHERE `id` = :id");
		$stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
		/*$stmt->bindParam(':sidebars_id', $data['sidebars_id'], PDO::PARAM_STR);*/
		$stmt->bindParam(':languages_id', $data['language'], PDO::PARAM_STR);
		$stmt->bindParam(':edit_date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':edited_by', $user, PDO::PARAM_INT);
		$stmt->bindParam(':keywords', $data['keywords'], PDO::PARAM_STR);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Delete page from database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `pages` WHERE `id` = :id");
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
			$stmt = $this->mysqli->prepare( "
				SELECT `p`.`id`, `title`, `content`, `sidebars_id`, `p`.`languages_id`, `language`, `create_date`, `edit_date`, `u`.`first_name` AS `c_first_name`, `u`.`last_name` AS `c_last_name`, `us`.`first_name` AS `e_first_name`, `us`.`last_name` AS `e_last_name`, `keywords`
				FROM `pages` `p`
				JOIN `languages` `l`
					 ON `l`.`id` = `p`.`languages_id`
				JOIN `users` `u`
					 ON `u`.`id` = `created_by` 
              	LEFT JOIN `users` `us`
              	 	ON `us`.`id` = `p`.`edited_by`
				WHERE `p`.`id` = :id
				LIMIT 1
			");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare( "
				SELECT `p`.`id`, `title`, `content`, `sidebars_id`, `p`.`languages_id`, `language`, `create_date`, `edit_date`, `u`.`first_name` AS `c_first_name`, `u`.`last_name` AS `c_last_name`, `us`.`first_name` AS `e_first_name`, `us`.`last_name` AS `e_last_name`, `keywords`
				FROM `pages` `p`
				JOIN `languages` `l`
					 ON `l`.`id` = `p`.`languages_id`
				JOIN `users` `u`
					 ON `u`.`id` = `created_by` 
              	LEFT JOIN `users` `us`
              	 	ON `us`.`id` = `p`.`edited_by`
			" );
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