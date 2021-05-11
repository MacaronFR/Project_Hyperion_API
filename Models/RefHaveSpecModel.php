<?php


namespace Hyperion\API;

require_once "autoload.php";

class RefHaveSpecModel extends Model{
    protected string $id_name = "id_ref_have_spec";
    protected string $table_name = "REF_HAVE_SPEC";
    protected array $column = [
        "product"=>"id_product",
        "spec"=>"id_spec",
		"value" => "value"
    ];
	public function selectAllBySpec(int $spec, int $iteration = 0, bool $limit = true): array|false{
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE id_spec=:spec";
		if($limit)
			$sql .= " LIMIT $start, $this->max_row";
		return $this->prepared_query($sql,["spec"=>$spec]);
	}

	public function selectAllFromRef(int $id_ref): array|false{
		$sql = "SELECT id_product as product, id_spec as spec, id_ref_have_spec as id, value as value FROM REF_HAVE_SPEC WHERE id_ref_have_spec=:id";
		return $this->prepared_query($sql, ['id' => $id_ref]);
	}

	public function deleteFromRef(int $id_ref): bool{
		$sql = "DELETE FROM REF_HAVE_SPEC WHERE id_product=:id";
		return $this->prepared_query($sql, ['id' => $id_ref] ,fetch: false);
	}
}