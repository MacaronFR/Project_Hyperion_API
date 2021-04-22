<?php


namespace Hyperion\API;

require_once "autoload.php";

class ReferenceModel extends Model{
	protected string $id_name = "id_reference";
	protected string $table_name = "REFERENCE_PRODUCTS";
	protected array $column = [
		"selling" => "selling_price",
		"buying" => "buying_price",
		"type" => "type"
	];

	public function selectAllByType(int $type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE RP.type=:id LIMIT $start, 500;";
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

	public function selectAllByMark(string $mark, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
    				INNER JOIN TYPES T on RP.type = T.id_type
    				INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"mark\" AND value=:mark LIMIT $start, 500;";
		$references = $this->prepared_query($sql, ["mark" => $mark]);
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

	public function selectAllByTypeMark(int $type, string $mark, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT RP.id_product as id, RP.buying_price as buying_price, RP.selling_price as selling_price, T.type as type FROM REFERENCE_PRODUCTS RP
    				INNER JOIN TYPES T on RP.type = T.id_type
    				INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
    				INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"mark\" AND value=:mark AND RP.type=:type LIMIT $start, 500;";
		$references = $this->prepared_query($sql, ["type" => $type, "mark" => $mark]);
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
		$sql1 = "SELECT S.* FROM REFERENCE_PRODUCTS RP INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE RP.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$ref = [];
		if($ref_spec !== false){
			foreach($ref_spec as $spec){
				if(isset($ref['spec'][$spec['name']])){
					if(is_array($ref['spec'][$spec['name']])){
						$ref['spec'][$spec['name']][] = $spec["value"];
					}else{
						$ref['spec'][$spec['name']] = [$ref['spec'][$spec['name']], $spec["value"]];
					}
				}else{
					$ref["spec"][$spec["name"]] = $spec["value"];
				}
			}
			return $ref;
		}
		return false;
	}

	public function selectAllMarkType(int $type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT S.value, FROM REFERENCE_PRODUCTS RP
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE S.name=\"mark\" AND id_type=:type GROUP BY S.value LIMIT $start,500;";
		return $this->prepared_query($sql, ["type" => $type]);
	}

	public function selectAllModelByMark(string $mark, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql_ref_mark = "SELECT RP.id_product as id, T.type FROM REFERENCE_PRODUCTS RP
    						INNER JOIN TYPES T on RP.type = T.id_type
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark LIMIT $start,500;";
		$sql_model = 	"SELECT value FROM SPECIFICATION S
							INNER JOIN REF_HAVE_SPEC RHS on S.id_specification = RHS.id_spec
						WHERE id_product=:id AND name=\"model\";";
		$ref_mark = $this->prepared_query($sql_ref_mark, ["mark" => $mark]);
		if($ref_mark === false || count($ref_mark) === 0){
			return $ref_mark;
		}
		foreach($ref_mark as $item){
			$models[$item['type']][] = $this->prepared_query($sql_model, ["id" => $item['id']], unique: true)["value"];
		}
		return $models;
	}

	public function selectAllModelByTypeMark(int $type, string $mark, int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql_ref_mark_type = "SELECT RP.id_product as id FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark AND type=:type LIMIT $start,500;";
		$sql_model = 	"SELECT value FROM SPECIFICATION S
							INNER JOIN REF_HAVE_SPEC RHS on S.id_specification = RHS.id_spec
						WHERE id_product=:id AND name=\"model\";";
		$ref_mark_type = $this->prepared_query($sql_ref_mark_type, ["type" => $type, "mark" => $mark]);
		if($ref_mark_type === false || count($ref_mark_type) === 0){
			return $ref_mark_type;
		}
		foreach($ref_mark_type as $item){
			$models[] = $this->prepared_query($sql_model, ["id" => $item['id']], unique: true)["value"];
		}
		return $models;
	}

	public function selectByModel(string $model): array|false{
		$sql = "SELECT RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type AS type FROM REFERENCE_PRODUCTS RP
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
					INNER JOIN TYPES T on RP.type = T.id_type
				WHERE name=\"model\" AND value=:model;";
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

	public function selectByMarkModel(string $mark, string $model): array|false{
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (SELECT COUNT(RP.id_product) as count, RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type as type
					  FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
							INNER JOIN TYPES T on RP.type = T.id_type
					  WHERE (name = \"model\" AND value = :model)
						 OR (name = \"mark\" AND value = :mark)
					  GROUP BY RP.id_product 
					 ) S
				WHERE count=2;";
		$references = $this->prepared_query($sql, ['model' => $model, 'mark' => $mark], unique: true);
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
				WHERE name=\"model\" AND value=:model AND RP.type=:type;";
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

	public function selectByTypeMarkModel(int $type, string $mark, string $model): array|false{
		$sql = "SELECT id, buying_price, selling_price, type
				FROM (
				    SELECT COUNT(RP.id_product) as count, RP.id_product as id, RP.selling_price as selling_price, RP.buying_price as buying_price, T.type as type
					FROM REFERENCE_PRODUCTS RP 
						INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
						INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						INNER JOIN TYPES T on RP.type = T.id_type
					WHERE ((name = \"model\" AND value = :model)
						OR (name = \"mark\" AND value = :mark))
						AND RP.type=:type
					GROUP BY RP.id_product 
					) S
				WHERE count=2;";
		$references = $this->prepared_query($sql, ['type' => $type, 'model' => $model, 'mark' => $mark], unique: true);
		if($references === false || empty($references)){
			return $references;
		}
		$spec = $this->selectWithDetail($references['id']);
		if($spec === false){
			return false;
		}
		return array_merge($references, $spec["spec"]);
	}

	public function selectAllMark(int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT value FROM SPECIFICATION S WHERE name=\"mark\" LIMIT $start,500";
		$marks = $this->query($sql);
		foreach($marks as &$mark){
			$mark = $mark['value'];
		}
		return $marks;
	}

	public function selectAllModel(int $iteration = 0): array|false{
		$start = $iteration * 500;
		$sql = "SELECT value FROM SPECIFICATION S WHERE name=\"model\" LIMIT $start,500";
		$models = $this->query($sql);
		foreach($models as &$model){
			$model = $model['value'];
		}
		return $models;
	}
}