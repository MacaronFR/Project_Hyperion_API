<?php


namespace Hyperion\API;
require_once "autoload.php";

 class CartModel extends Model{
 protected string $table_name = "CART";
 protected string $id_name = "id_cart";
 protected array $column = ["status" => "status"];
}