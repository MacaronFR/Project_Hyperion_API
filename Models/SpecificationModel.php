<?php


namespace Hyperion\API;

require_once "autoload.php";

class SpecificationModel extends Model{
    protected string $id_name = "id_specification";
    protected string $table_name = "SPECIFICATION";
    protected array $column = [
        "name"=>"name",
        "value"=>"value"
    ];
    protected int $max_row = 10;

	public function selectIdentical(array $fields): array|false{
		$sql = "SELECT ";
		foreach($this->column as $name => $column){
			$sql .= "$column as $name, ";
		}
		$sql .= "$this->id_name as id ";
		$sql .= "FROM $this->table_name WHERE name=:name AND value=:value;";
		return $this->prepared_query($sql,$fields, unique: true);
	}
	public function selectAllName(int $iteration = 0, bool $limit = true): array|false{
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		$sql .= " $this->column['name'] as name";
		$sql .= " FROM $this->table_name";
		if($limit)
			$sql .= " LIMIT $start, $this->max_row";
		return $this->query($sql);
	}

	public function selectAllMark(int $iteration = 0, bool $limit = true): array|false{
		$start = $iteration * $this->max_row;
		$sql = "SELECT id_specification as id, value FROM SPECIFICATION WHERE name=\"mark\"";
		if($limit){
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectTotalMark(): array|false{
		$sql = "SELECT COUNT(id_specification) as count FROM SPECIFICATION WHERE name=\"mark\"";
		return $this->query($sql, unique: true);
	}

	public function selectAllModelByTypeMark(int $type, string $mark, int $iteration = 0, bool $limit = true): array|false{
		$start = $iteration * $this->max_row;
		$sql_ref_mark_type = "SELECT RP.id_product as id FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark AND type=:type";
		if($limit){
			$sql_ref_mark_type .= " LIMIT $start, $this->max_row;";
		}
		$sql_model = "SELECT id_specification as id, value FROM SPECIFICATION S
							INNER JOIN REF_HAVE_SPEC RHS on S.id_specification = RHS.id_spec
						WHERE id_product=:id AND name=\"model\";";
		$ref_mark_type = $this->prepared_query($sql_ref_mark_type, ["type" => $type, "mark" => $mark]);
		if($ref_mark_type === false || count($ref_mark_type) === 0){
			return $ref_mark_type;
		}
		foreach($ref_mark_type as $item){
			$models[] = $this->prepared_query($sql_model, ["id" => $item['id']], unique: true);
		}
		return $models;
	}

	public function selectTotalModelByTypeMark(int $type, string $mark): int|false{
		$sql_ref_mark_type = "SELECT COUNT(RP.id_product) as count FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark AND type=:type";
		$res = $this->prepared_query($sql_ref_mark_type, ["type" => $type, "mark" => $mark], unique: true);
		if($res === false){
			return false;
		}
		return (int)$res['count'];
	}

	public function selectAllModel(int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT
					value as value,
       				TYPES.type as type
				FROM SPECIFICATION S
				INNER JOIN
				    REF_HAVE_SPEC
				INNER JOIN
					REFERENCE_PRODUCTS
				INNER JOIN
				    TYPES
				ON 
					REF_HAVE_SPEC.id_product = REFERENCE_PRODUCTS.id_product AND
					REF_HAVE_SPEC.id_spec = S.id_specification AND
					REFERENCE_PRODUCTS.type = TYPES.id_type
				WHERE name=\"model\"";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		$models = $this->query($sql);
		if($models === false){
			return false;
		}
		$result = [];
		foreach($models as $model){
			$result[$model['type']][] = $model['value'];
		}
		return $result;
	}

	public function selectTotalModel(): int|false{
		$sql = "SELECT COUNT(id_specification) as count FROM SPECIFICATION S WHERE name=\"model\"";
		$res = $this->query($sql, unique: true);
		if($res === false){
			return false;
		}
		return (int)$res['count'];
	}

	public function selectAllModelByType(int $type, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT
					S.`value`
				FROM
					SPECIFICATION AS S
					INNER JOIN
					REFERENCE_PRODUCTS
					INNER JOIN
					REF_HAVE_SPEC
					ON 
						REFERENCE_PRODUCTS.id_product = REF_HAVE_SPEC.id_product AND
						S.id_specification = REF_HAVE_SPEC.id_spec
				WHERE
					REFERENCE_PRODUCTS.type = :type AND
					S.`name` = \"model\"";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ['type' => $type]);
	}

	public function selectTotalModelByType(int $type): array|false{
		$sql = "SELECT
					COUNT(S.`value`) as count
				FROM
					SPECIFICATION AS S
					INNER JOIN
					REFERENCE_PRODUCTS
					INNER JOIN
					REF_HAVE_SPEC
					ON 
						REFERENCE_PRODUCTS.id_product = REF_HAVE_SPEC.id_product AND
						S.id_specification = REF_HAVE_SPEC.id_spec
				WHERE
					REFERENCE_PRODUCTS.type = :type AND
					S.`name` = \"model\"";
		return $this->prepared_query($sql, ['type' => $type], unique: true);
	}
}