<?php


namespace Hyperion\API;

require_once "autoload.php";

class RefHaveSpecModel extends Model{
    protected string $id_name = "id_ref_have_spec";
    protected string $table_name = "REF_HAVE_SPEC";
    protected array $column = [
        "product"=>"id_product",
        "spec"=>"id_spec"
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

}