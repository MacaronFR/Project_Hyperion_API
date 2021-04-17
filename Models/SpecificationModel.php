<?php


namespace Hyperion\API;


class SpecificationModel extends Model{
    protected string $id_name = "id_specification";
    protected string $table_name = "SPECIFICATION";
    protected array $column = [
        "specification"=>"id_specification",
        "name"=>"name",
        "value"=>"value"
    ];

}