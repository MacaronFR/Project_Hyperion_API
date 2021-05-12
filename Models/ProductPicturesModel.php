<?php


namespace Hyperion\API;


class ProductPicturesModel extends Model{
	protected string $id_name = "id_picture";
	protected string $table_name = "PRODUCT_PICTURES";
	protected array $column = [
		"product" => "id_product",
		"file" => "id_file"
	];

	public function selectAllByProduct(int $product, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT ";
		foreach($this->column as $p => $n){
			$sql .= "$n as $p, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE " . $this->column['product'] . "=:id";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ['id' => $product]);
	}
}