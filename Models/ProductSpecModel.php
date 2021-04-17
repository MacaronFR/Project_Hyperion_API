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
}