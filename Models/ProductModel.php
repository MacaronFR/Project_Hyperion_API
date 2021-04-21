<?php


namespace Hyperion\API;
require_once "autoload.php";

class ProductModel extends Model{
	protected string $table_name = "PRODUCTS";
	protected string $id_name = "id_product";
	protected array $column = [
		"state" => "state",
		"sell_p" => "selling_price",
		"buy_p" => "buying_price",
		"status" => "status",
		"offer" => "id_offers",
		"ref" => "id_ref",
		"buy_d" => "buying_date",
		"sell_d" => "selling_date"
	];

	public function selectAllByType(int $id_type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT PRODUCTS.id_product as id FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product WHERE RP.type=:id LIMIT $start, 500;";
		return $this->prepared_query($sql, ["id" => $id_type]);
	}

	public function selectWithDetail(int $id): array|false{
		$sql1 = "SELECT S.* FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$sql2 = "SELECT S.* FROM PRODUCTS INNER JOIN PRODUCT_HAVE_SPEC PHS on PRODUCTS.id_product = PHS.id_product INNER JOIN SPECIFICATION S on PHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$prod_spec = $this->prepared_query($sql2, ["id" => $id]);
		$prod = [];
		if($ref_spec !== false && $prod_spec !== false){
			foreach($ref_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			foreach($prod_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			return $prod;
		}
		return false;
	}

	public function selectAllDetails(int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$query1 = "SELECT PROD.id_product, PROD.buying_price, PROD.selling_price, T.type, REF.id_product as id_ref FROM PRODUCTS PROD INNER JOIN REFERENCE_PRODUCTS REF ON PROD.id_ref = REF.id_product INNER JOIN TYPES T on REF.type = T.id_type LIMIT $start, 500;";
		$products = $this->query($query1);
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id']));
		}
		return $products;
	}

	public function selectAllByMark(string $mark, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT P.id_product as id, P.buying_price as buying_price, P.selling_price as selling_price, T.type as type FROM PRODUCTS P
					INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"mark\" AND S.value=:mark LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["mark" => $mark]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeMark(int $type, string $mark, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT PRODUCTS.id_product as id FROM PRODUCTS
					INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"mark\" AND S.value=:mark AND T.id_type=:type LIMIT $start,500;";
		return $this->prepared_query($sql, ["type" => $type, "mark" => $mark]);
	}

	public function selectAllByModel(string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type AS type FROM PRODUCTS P
    				INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND value=:model LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["model" => $model]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeModel(int $type, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type AS type FROM PRODUCTS P
    				INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND value=:model AND RP.type=:type LIMIT $start,500;";
		$products = $this->prepared_query($sql, ["model" => $model, 'type' => $type]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}

	public function selectAllByMarkModel(string $mark, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (SELECT COUNT(P.id_product) as count, P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type as type
					  FROM PRODUCTS P 
							   INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
							   INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							   INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
							   INNER JOIN TYPES T on RP.type = T.id_type
					  WHERE (name = \"model\" AND value = :model)
						 OR (name = \"mark\" AND value = :mark)
					  GROUP BY P.id_product 
					 ) S
				WHERE count=2 LIMIT $start,500;";
		$products = $this->prepared_query($sql, ['model' => $model, 'mark' => $mark]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}

	public function selectAllByTypeMarkModel(int $type, string $mark, string $model, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (SELECT COUNT(P.id_product) as count, P.id_product as id, P.selling_price as selling_price, P.buying_price as buying_price, T.type as type
					  FROM PRODUCTS P 
							   INNER JOIN REFERENCE_PRODUCTS RP on P.id_ref = RP.id_product
							   INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							   INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
							   INNER JOIN TYPES T on RP.type = T.id_type
					  WHERE ((name = \"model\" AND value = :model)
						 OR (name = \"mark\" AND value = :mark))
						 AND RP.type=:type
					  GROUP BY P.id_product 
					 ) S
				WHERE count=2 LIMIT $start,500;";
		$products = $this->prepared_query($sql, ['type' => $type, 'model' => $model, 'mark' => $mark]);
		if($products === false || empty($products)){
			return $products;
		}
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id'])["spec"]);
		}
		return $products;
	}
}