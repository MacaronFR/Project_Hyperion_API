<?php


namespace Hyperion\API;


class ProductSpecModel extends Model {
    protected string $id_name = "id_product_have_spec";
    protected string $table_name = "PRODUCT_HAVE_SPEC";
    protected array $column = [
        "id"=>"id_product_have_spec",
        "product"=>"id_product",
        "spec"=>"id_spec"
    ];
}