<?php
class Cake extends Database implements Plugin {
	/**
	 * Add cake
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		$stmt = $this->mysqli->prepare("INSERT INTO `cakes` (`cake`, `description`) VALUES (:cake, :description)");
		$stmt->bindParam(':cake', $data['cake'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
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
	 * Get cake info
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("
				SELECT `c`.`id`, `cake`, `description`, SUM(i.calories) AS `calories`, SUM(i.buy_price) AS `buy_price`, SUM(i.sell_price) AS `sell_price`
				FROM `cakes` `c`
				JOIN `recipes` `r`
					ON `c`.`id` = `r`.`cakes_id`
				JOIN `ingredients` `i`
					ON `r`.`ingredients_id` = `i`.`id`
				WHERE `c`.`id` = :id
				GROUP BY `c`.`id`
			");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("
				SELECT `c`.`id`, `cake`, `description`, SUM(i.calories) AS `calories`, SUM(i.buy_price) AS `buy_price`, SUM(i.sell_price) AS `sell_price`
				FROM `cakes` `c`
				JOIN `recipes` `r`
					ON `c`.`id` = `r`.`cakes_id`
				JOIN `ingredients` `i`
					ON `r`.`ingredients_id` = `i`.`id`
				GROUP BY `c`.`id`
			");
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
	 * Delete cake
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `cakes` WHERE `id` = :id");
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
	 * Edit cake
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$stmt = $this->mysqli->prepare("UPDATE `cakes` SET `cake` = :cake, `description` = :description WHERE `id` = :id");
		$stmt->bindParam(':cake', $data['cake'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function addRecipe( array $data ) {

	}

	public function dataRecipe( int $id = null ) {

	}

	public function deleteRecipe( int $id ) {

	}

	public function editRecipe( array $data ) {

	}

	/**
	 * Add ingredient
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function addIngredient( array $data ) {
		$stmt = $this->mysqli->prepare("
			INSERT INTO `ingredients` (`ingredient`, `calories`, `allergies`, `buy_price`, `sell_price`, `stock`, `unit`)
			VALUES (:ingredient, :calories, :allergies, :buy_price, :sell_price, :stock, :unit)	
		");
		$stmt->bindParam(':ingredient', $data['ingredient'], PDO::PARAM_STR);
		$stmt->bindParam(':calories', $data['calories'], PDO::PARAM_INT);
		$stmt->bindParam(':allergies', $data['allergies'], PDO::PARAM_STR);
		$stmt->bindParam(':buy_price', $data['buy_price'], PDO::PARAM_STR);
		$stmt->bindParam(':sell_price', $data['sell_price'], PDO::PARAM_STR);
		$stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
		$stmt->bindParam(':unit', $data['unit'], PDO::PARAM_STR);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return true;
		}
	}

	/**
	 * Get data from ingredients
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function dataIngredient( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("SELECT `id`, `ingredient`, `calories`, `allergies`, `buy_price`, `sell_price`, `stock`, `unit` FROM `ingredients` WHERE `id` = : id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("SELECT `id`, `ingredient`, `calories`, `allergies`, `buy_price`, `sell_price`, `stock`, `unit` FROM `ingredients`");
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
	 * Delete ingredient
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function deleteIngredient( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `ingredients` WHERE `id` = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return true;
		}
	}

	/**
	 * Edit ingredient
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function editIngredient( array $data ) {
		$stmt = $this->mysqli->prepare("
			UPDATE `ingredients` 
			SET `ingredient` = :ingredient, `calories` = :calories, `allergies` = :allergies , `buy_price` = :buy_price, `sell_price` = :sell_price, `stock` = :stock, `unit` = :unit WHERE `id` = :id");
		$stmt->bindParam(':ingredient', $data['ingredient'], PDO::PARAM_STR);
		$stmt->bindParam(':calories', $data['calories'], PDO::PARAM_INT);
		$stmt->bindParam(':allergies', $data['allergies'], PDO::PARAM_STR);
		$stmt->bindParam(':buy_price', $data['buy_price'], PDO::PARAM_STR);
		$stmt->bindParam(':sell_price', $data['sell_price'], PDO::PARAM_STR);
		$stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
		$stmt->bindParam(':unit', $data['unit'], PDO::PARAM_STR);
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
}