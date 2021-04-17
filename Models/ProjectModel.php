<?php


namespace Hyperion\API;


class ProjectModel extends Model{
    protected string $table_name = "PROJECTS";
    protected string $id_name = "id_projects";
    protected array $column = [
        "project"=>"id_project",
        "name"=>"name",
        "description"=>"description",
        "start"=>"start",
        "duration"=>"duration"
    ];

}