<?php


namespace Hyperion\API;

require_once "autoload.php";

class ProjectModel extends Model{
    protected string $table_name = "PROJECTS";
    protected string $id_name = "id_project";
    protected array $column = [
        "name"=>"name",
        "description"=>"description",
        "start"=>"start",
        "duration"=>"duration",
	    "logo"=>"logo",
		"valid"=>"valid"
    ];
	protected int $max_row = 10;

	public function selectPopular(int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->id_name as id, SUM(CONTRIBUTE.`value`) AS contribution FROM $this->table_name LEFT JOIN CONTRIBUTE ON $this->id_name = CONTRIBUTE.id_project WHERE $this->table_name.valid = 1 GROUP BY $this->table_name.$this->id_name ORDER BY CONTRIBUTION DESC";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);

	}

}