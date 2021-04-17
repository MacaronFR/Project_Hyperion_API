<?php


namespace Hyperion\API;


class RefHaveSpecModel extends Model{
    protected string $id_name = "id_ref_have_spec";
    protected string $table_name = "REF_HAVE_SPEC";
    protected array $column = [
        "id_ref"=>"id_ref_have_pec",
        "product"=>"id_product",
        "spec"=>"id_spec"
    ];
}