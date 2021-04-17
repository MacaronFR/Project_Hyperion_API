<?php


namespace Hyperion\API;

require_once "autoload.php";

class ReferenceModel extends Model{
protected string $id_name = "id_reference";
protected string $table_name = "REFERENCE_PRODUCTS";
protected array $column = [
    "selling"=>"selling_price",
    "buying"=>"buying_price",
    "type"=>"type"
];
}