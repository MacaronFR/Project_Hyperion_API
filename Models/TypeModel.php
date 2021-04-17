<?php


namespace Hyperion\API;


class TypeModel extends Model{
    protected string $id_name = "id_type";
    protected string $table_name = "TYPES";
    protected array $column = [
        "type"=>"type",
        "category"=>"category"
    ];

    public function selectByCategory(int $id_category, int $iteration = 0): array|false{
        $start = $iteration*500;
       return  $this->prepared_query("SELECT type,category FROM TYPES WHERE category=:id LIMIT $start,500",
       ["id"=>$id_category]);
    }
}