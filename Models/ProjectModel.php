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
		"valid"=>"valid",
		"RNA" => "RNA"
    ];
	protected int $max_row = 10;

	public function selectPopular(int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->table_name.$this->id_name as id, SUM(CONTRIBUTE.`value`) AS contribution FROM $this->table_name LEFT JOIN CONTRIBUTE ON $this->table_name.$this->id_name = CONTRIBUTE.id_project WHERE $this->table_name.valid = 1 AND DATEDIFF(DATE_ADD(start, INTERVAL duration DAY), DATE(NOW())) >= 0 GROUP BY $this->table_name.$this->id_name ORDER BY contribution DESC";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllProject(int $iteration = 0, bool $limit = true, int $valid = 1): array|false{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->table_name.$this->id_name as id, SUM(CONTRIBUTE.`value`) AS contribution FROM $this->table_name LEFT JOIN CONTRIBUTE ON $this->table_name.$this->id_name = CONTRIBUTE.id_project WHERE valid=$valid AND DATEDIFF(DATE_ADD(start, INTERVAL duration DAY), DATE(NOW())) >= 0 GROUP BY $this->table_name.$this->id_name";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllValidLast(int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->table_name.$this->id_name as id, SUM(CONTRIBUTE.`value`) AS contribution FROM $this->table_name LEFT JOIN CONTRIBUTE ON $this->table_name.$this->id_name = CONTRIBUTE.id_project WHERE valid=1 AND DATEDIFF(DATE_ADD(start, INTERVAL duration DAY), DATE(NOW())) >= 0 GROUP BY $this->table_name.$this->id_name ORDER BY start DESC";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllProjectFilter(string $search, string $order, string $sort, int $iteration = 0, int $valid = 1): array|false{
		$start = $this->max_row * $iteration;
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->table_name.$this->id_name as id, SUM(CONTRIBUTE.`value`) AS contribution FROM $this->table_name LEFT JOIN CONTRIBUTE ON $this->table_name.$this->id_name = CONTRIBUTE.id_project WHERE valid=$valid AND DATEDIFF(DATE_ADD(start, INTERVAL duration DAY), DATE(NOW())) >= 0 ";
		$sql .= "AND (name LIKE :search OR description LIKE :search OR RNA LIKE :search) ";
		$sql .="GROUP BY $this->table_name.$this->id_name ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%" . $search . "%";
		return $this->prepared_query($sql, ["search" => $search]);
	}

	public function checkProject(string $rna): array|false{
		$url = "https://entreprise.data.gouv.fr/api/rna/v1/id/$rna";
		$request = curl_init($url);
		if(!empty(http_response_code($request))){
			return $request;
		}else{
			return response(204,"No Content");
		}
}

}