<?php


namespace Hyperion\API;

require_once "autoload.php";

class ReferenceModel extends Model{
	protected string $id_name = "id_product";
	protected string $table_name = "REFERENCE_PRODUCTS";
	protected array $column = [
		"selling" => "selling_price",
		"buying" => "buying_price",
		"type" => "type"
	];
	protected int $max_row = 10;

	public function selectAllByType(int $type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE RP.type=:id LIMIT $start, $this->max_row;";
		$references = $this->prepared_query($sql, ["id" => $type]);
		if($references === false || empty($references)){
			return $references;
		}
		foreach($references as &$ref){
			$spec = $this->selectWithDetail($ref['id']);
			if($spec === false){
				return false;
			}
			$ref = array_merge($ref, $spec["spec"]);
		}
		return $references;
	}

	public function selectAllByBrand(string $brand, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
    				INNER JOIN TYPES T on RP.type = T.id_type
    				INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"brand\" AND S.value=:brand LIMIT $start, $this->max_row;";
		$references = $this->prepared_query($sql, ["brand" => $brand]);
		if($references === false || empty($references)){
			return $references;
		}
		foreach($references as &$ref){
			$spec = $this->selectWithDetail($ref['id']);
			if($spec === false){
				return $spec;
			}
			$ref = array_merge($ref, $spec["spec"]);
		}
		return $references;
	}

	public function selectAllByTypeBrand(int $type, string $brand, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
    				INNER JOIN TYPES T on RP.type = T.id_type
    				INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"brand\" AND S.value=:brand AND RP.type=:type LIMIT $start, $this->max_row;";
		$references = $this->prepared_query($sql, ["type" => $type, "brand" => $brand]);
		if($references === false || empty($references)){
			return $references;
		}
		foreach($references as &$ref){
			$spec = $this->selectWithDetail($ref['id']);
			if($spec === false){
				return false;
			}
			$ref = array_merge($ref, $spec["spec"]);
		}
		return $references;
	}

	public function selectWithDetail(int $id): array|false{
		$sql1 = "SELECT S.*, RHS.value as bonus FROM REFERENCE_PRODUCTS RP INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE RP.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$ref = [];
		if($ref_spec !== false){
			foreach($ref_spec as $spec){
				if(isset($ref['spec'][$spec['name']])){
					if(is_array($ref['spec'][$spec['name']])){
						$ref['spec'][$spec['name']][] = [$spec["value"], $spec['bonus']];
					}else{
						$ref['spec'][$spec['name']] = [$ref['spec'][$spec['name']], [$spec["value"], $spec['bonus']]];
					}
				}else{
					$ref["spec"][$spec["name"]] = [[$spec["value"], $spec['bonus']]];
				}
			}
			return $ref;
		}
		return false;
	}

	public function selectAllBrandType(int $type, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT S.value as value, S.id_specification as id FROM REFERENCE_PRODUCTS RP
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"brand\" AND id_type=:type GROUP BY S.value";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= "LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ["type" => $type]);
	}

	public function selectAllModelByBrand(string $brand, int $iteration = 0, $limit = true): array|false{
		$start = $iteration * $this->max_row;
		$sql_ref_brand = "SELECT RP.id_product as id, T.type FROM REFERENCE_PRODUCTS RP
    						INNER JOIN TYPES T on RP.type = T.id_type
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"brand\" and S.value=:brand";
		if($limit){
			$sql_ref_brand .= " LIMIT $start, $this->max_row;";
		}
		$sql_model = 	"SELECT S.value FROM SPECIFICATION S
							INNER JOIN REF_HAVE_SPEC RHS on S.id_specification = RHS.id_spec
						WHERE id_product=:id AND name=\"model\";";
		$ref_brand = $this->prepared_query($sql_ref_brand, ["brand" => $brand]);
		if($ref_brand === false || count($ref_brand) === 0){
			return $ref_brand;
		}
		foreach($ref_brand as $item){
			$models[$item['type']][] = $this->prepared_query($sql_model, ["id" => $item['id']], unique: true)["value"];
		}
		return $models;
	}

	public function selectTotalModelByBrand(string $brand){
		$sql_ref_brand = "SELECT COUNT(RP.id_product) as count FROM REFERENCE_PRODUCTS RP
    						INNER JOIN TYPES T on RP.type = T.id_type
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"brand\" and S.value=:brand";
		return $this->prepared_query($sql_ref_brand, ["brand" => $brand], unique: true);
	}

	public function selectByModel(string $model): array|false{
		$sql = "SELECT RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type AS type FROM REFERENCE_PRODUCTS RP
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND S.value=:model;";
		$references = $this->prepared_query($sql, ["model" => $model], unique: true);
		if($references === false || empty($references)){
			return $references;
		}
		$spec = $this->selectWithDetail($references['id']);
		if($spec === false){
			return false;
		}
		return array_merge($references, $spec["spec"]);
	}

	public function selectByBrandModel(string $brand, string $model): array|false{
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (SELECT COUNT(RP.id_product) as count, RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type as type
					  FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
							INNER JOIN TYPES T on RP.type = T.id_type
					  WHERE (name = \"model\" AND S.value = :model)
						 OR (name = \"brand\" AND S.value = :brand)
					  GROUP BY RP.id_product 
					 ) S
				WHERE count=2;";
		$references = $this->prepared_query($sql, ['model' => $model, 'brand' => $brand], unique: true);
		if($references === false || empty($references)){
			return $references;
		}
		$spec = $this->selectWithDetail($references['id']);
		if($spec === false){
			return false;
		}
		return array_merge($references, $spec["spec"]);
	}

	public function selectByTypeModel(int $type, string $model): array|false{
		$sql = "SELECT RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type AS type FROM REFERENCE_PRODUCTS RP
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND S.value=:model AND RP.type=:type;";
		$references = $this->prepared_query($sql, ["model" => $model, 'type' => $type], unique: true);
		if($references === false || empty($references)){
			return $references;
		}
		$spec = $this->selectWithDetail($references['id']);
		if($spec === false){
			return false;
		}
		return array_merge($references, $spec["spec"]);
	}

	public function selectByTypeBrandModel(int $type, string $brand, string $model): array|false{
		$sql = " SELECT COUNT(RP.id_product) as count, RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type as type
					FROM REFERENCE_PRODUCTS RP 
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						INNER JOIN TYPES T on RP.type = T.id_type
					WHERE ((S.name = \"model\" AND S.value = :model)
						OR (S.name = \"brand\" AND S.value = :brand))
						AND RP.type=:type
					GROUP BY RP.id_product HAVING COUNT(RP.id_product) = 2;";
		$references = $this->prepared_query($sql, ['type' => $type, 'model' => $model, 'brand' => $brand], unique: true);
		if($references === false || empty($references)){
			return $references;
		}
		$spec = $this->selectWithDetail($references['id']);
		if($spec === false){
			return false;
		}
		$spec = $spec['spec'];
		$references['model'] = $spec['model'][0][0];
		$references['brand'] = $spec['brand'][0][0];
		unset($spec['model'], $spec['brand']);
		$references['spec'] = $spec;
		return $references;
	}

	public function selectAllFilterWithDetail(string $search, string $order, string $sort, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE ";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%" . $search . "%";
		return $this->prepared_query($sql, ["search" => $search]);
	}
}