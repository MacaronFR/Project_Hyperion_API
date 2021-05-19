<?php


namespace Hyperion\API;

require_once "autoload.php";

class OffersModel extends Model{
	protected string $id_name = "id_offer";
	protected string $table_name = "OFFERS";
	protected array $column = [
		"offer" => "offer",
		"counter_offer" => "counter_offer",
		"status" => "status",
		"user" => "id_user",
		"date" => "date_creation"
	];
	protected int $max_row = 10;


	public function selectAllPending(int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE status<5";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllPendingByUser(int $user, int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE status<5 AND id_user=:user";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ['user' => $user]);
	}

	public function selectTotalPending():int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE status < 5";
		$res = $this->query($sql, unique: true);
		if($res === false){
			return false;
		}
		return $res['count'];
	}
	public function selectTotalPendingByUser(int $user):int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE status < 5 AND id_user=:user";
		$res = $this->prepared_query($sql, ['user' => $user], unique: true);
		if($res === false){
			return false;
		}
		return $res['count'];
	}

	public function selectAllTerminated(int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE status>4";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->query($sql);
	}

	public function selectAllTerminatedByUser(int $user, int $iteration = 0, bool $limit = true): false|array{
		$sql = "SELECT ";
		foreach($this->column as $p=>$c){
			$sql .= "$c as $p, ";
		}
		$sql .= "$this->id_name as id FROM $this->table_name WHERE status>4 AND id_user=:user";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= " LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ['user' => $user]);
	}

	public function selectTotalTerminated():int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE status > 4";
		$res = $this->query($sql, unique: true);
		if($res === false){
			return false;
		}
		return $res['count'];
	}
	public function selectTotalTerminatedByUser(int $user):int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE status > 4 AND id_user=:user";
		$res = $this->prepared_query($sql, ['user' => $user], unique: true);
		if($res === false){
			return false;
		}
		return $res['count'];
	}
}