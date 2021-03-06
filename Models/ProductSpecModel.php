<?php


namespace Hyperion\API;

require_once "autoload.php";

class ProductSpecModel extends Model {
    protected string $id_name = "id_product_have_spec";
    protected string $table_name = "PRODUCT_HAVE_SPEC";
    protected array $column = [
        "product"=>"id_product",
        "spec"=>"id_spec"
    ];

    public function selectAllByProduct(int $id_prod, int $iteration = 0, bool $limit = true): false|array{
    	$sql = "SELECT ";
    	foreach($this->column as $param => $column){
    		$sql .= "$column as $param, ";
		}
    	$sql .= "$this->id_name as id FROM $this->table_name WHERE " . $this->column['product'] . "=:id";
    	if($limit){
    		$start = $iteration * $this->max_row;
    		$sql .= " LIMIT $start, $this->max_row";
		}
    	return $this->prepared_query($sql, ['id' => $id_prod]);
	}

	public function selectBySpecProd(int $spec, int $product): array|false{
		$sql = "SELECT $this->id_name as id FROM $this->table_name WHERE id_spec=:spec AND id_product=:product";
		return $this->prepared_query($sql, ['spec' => $spec, 'product' => $product], unique: true);
	}
}