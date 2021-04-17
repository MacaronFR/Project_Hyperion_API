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
}