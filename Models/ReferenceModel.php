<?php


namespace Hyperion\API;


class ReferenceModel extends Model{
protected string $id_name = "id_reference";
protected string $table_name = "REFERENCE_PRODUCTS";
protected array $column = [
    "product"=>"id_product",
    "selling"=>"selling_price",
    "buying"=>"buying_price",
    "type"=>"type"
];
}