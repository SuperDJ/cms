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
		$stmt = $this->mysqli->prepare("INSERT INTO `cakes` (`cake`, `description`, `image`) VALUES (:cake, :description, :image)");
		$stmt->bindParam(':cake', $data['cake'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
		$stmt->bindParam(':image', $data['path'], PDO::PARAM_STR);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$insertID = $this->mysqli->lastInsertId();
			$stmt = null;

			$count = count( $data['recipe'] );
			$i = 0;
			$stmt = $this->mysqli->prepare("INSERT INTO `recipes` (`cakes_id`, `ingredients_id`) VALUES (:cakes_id, :ingredients_id)");
			foreach( $data['recipe'] as $key => $id ) {
				echo 'id: '.$id.' ';
				$stmt->bindParam(':cakes_id', $insertID, PDO::PARAM_INT);
				$stmt->bindParam(':ingredients_id', $id, PDO::PARAM_INT);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$stmt = null;
					$i++;
				} else {
					$stmt = null;
					return true;
				}
			}

			if( $count == $i ) {
				return true;
			} else {
				return false;
			}
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
				SELECT `c`.`id`, `cake`, `description`, `image`, SUM(i.calories) AS `calories`, SUM(i.buy_price) AS `buy_price`, SUM(i.sell_price) AS `sell_price`
				FROM `cakes` `c`
				LEFT JOIN `recipes` `r`
					ON `c`.`id` = `r`.`cakes_id`
				LEFT JOIN `ingredients` `i`
					ON `r`.`ingredients_id` = `i`.`id`
				WHERE `c`.`id` = :id
				GROUP BY `c`.`id`
				LIMIT 1
			");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("
				SELECT `c`.`id`, `cake`, `description`, `image`, SUM(i.calories) AS `calories`, SUM(i.buy_price) AS `buy_price`, SUM(i.sell_price) AS `sell_price`
				FROM `cakes` `c`
				LEFT JOIN `recipes` `r`
					ON `c`.`id` = `r`.`cakes_id`
				LEFT JOIN `ingredients` `i`
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
		$q = 0;
		$stmt = $this->mysqli->prepare("UPDATE `cakes` SET `cake` = :cake, `description` = :description, `image` = :image WHERE `id` = :id");
		$stmt->bindParam(':cake', $data['cake'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
		$stmt->bindParam(':image', $data['path'], PDO::PARAM_STR);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			$q++;
		} else {
			$stmt = null;
			$q++;
		}

		$recipes = $this->dataRecipe($data['id']);
		// Delete array
		$add = array();
		$delete = array();
		echo 'r';
		print_r($data['recipe']);
		foreach( $data['recipe'] as $key => $field ) {
			if( !in_array( $field, array_column( $recipes, 'ingredients_id' ) ) ) {
				$add[] = $field;
			}
		}

		foreach( $recipes as $key => $field ){

		}

		print_r($add);
		print_r($delete);
	}

	/**
	 * Data from recipes
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function dataRecipe( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("SELECT `id`, `cakes_id`, `ingredients_id` FROM `recipes` WHERE `cakes_id` = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("SELECT `id`, `cakes_id`, `ingredients_id` FROM `recipes`");
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
			$stmt = $this->mysqli->prepare("SELECT `id`, `ingredient`, `calories`, `allergies`, `buy_price`, `sell_price`, `stock`, `unit` FROM `ingredients` WHERE `id` = :id LIMIT 1");
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