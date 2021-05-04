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
			$sql .= " LIMIT $start,500";
		}
		return $this->query($sql);
	}

	public function selectAllModelByTypeMark(int $type, string $mark, int $iteration = 0, bool $limit = true): array|false{
		$start = $iteration * $this->max_row;
		$sql_ref_mark_type = "SELECT RP.id_product as id FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark AND type=:type";
		if($limit){
			$sql_ref_mark_type .= " LIMIT $start,500;";
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
		$sql_ref_mark_type = "SELECT COUNT(RP.id_product) as id FROM REFERENCE_PRODUCTS RP
							INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
							INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
						WHERE name=\"mark\" and value=:mark AND type=:type";
		return $this->prepared_query($sql_ref_mark_type, ["type" => $type, "mark" => $mark], unique: true);
	}
}