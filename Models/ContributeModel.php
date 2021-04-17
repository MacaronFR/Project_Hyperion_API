<?php


namespace Hyperion\API;


class ContributeModel extends Model{
    protected string $id_name = "id_contribute";
    protected string $table_name = "CONTRIBUTE";
    protected array $column = [
        "contributor"=>"id_contributor",
        "value"=>"value",
        "project"=>"id_project",
        "user"=>"id_user",
    ];
}