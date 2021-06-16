<?php


namespace Hyperion\API;

require_once "autoload.php";

class ProductInCartModel extends Model{
	protected string $id_name = "id_prod_in_cart";
	protected string $table_name = "PRODUCT_IN_CART";
	protected array $column = [
		"product" => "id_product",
		"cart" => "id_cart"
	];

	public function selectIdentical(int $cart, int $product): array|false{
		$sql = "SELECT $this->id_name as id FROM $this->table_name WHERE " . $this->column['cart'] ."=:cart AND " . $this->column['product'] . "=:product";
		return $this->prepared_query($sql, ['cart' => $cart, 'product' => $product], unique: true);
	}

	public function selectByCart(int $cart): array|false{
		$sql = "SELECT";
		foreach($this->column as $name => $item){
			$sql .= " $item as $name,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE " . $this->column['cart'] ."=:cart";
		return $this->prepared_query($sql, ['cart' => $cart]);
	}
}