<?php


namespace Hyperion\API;

require_once "autoload.php";

class ProjectModel extends Model{
    protected string $table_name = "PROJECTS";
    protected string $id_name = "id_projects";
    protected array $column = [
        "name"=>"name",
        "description"=>"description",
        "start"=>"start",
        "duration"=>"duration"
    ];

}