<?php


namespace Hyperion\API;


class TypeModel extends Model{
    protected string $id_name = "id_type";
    protected string $table_name = "TYPES";
    protected array $column = [
        "id_type"=>"id_type",
        "type"=>"type",
        "category"=>"category"
    ];
}