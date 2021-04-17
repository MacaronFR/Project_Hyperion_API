<?php


namespace Hyperion\API;

require_once "autoload.php";

class ProductInCartModel extends Model{
	protected string $id_name = "id_product_in_cart";
	protected string $table_name = "PRODUCT_IN_CART";
	protected array $column = [
		"product" => "id_product",
		"cart" => "id_cart"
	];

}