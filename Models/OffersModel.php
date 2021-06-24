<?php


namespace Hyperion\API;

require_once "autoload.php";

class OffersModel extends Model{
	protected string $id_name = "id_offer";
	protected string $table_name = "OFFERS";
	public array $column = [
		"offer" => "offer",
		"counter_offer" => "counter_offer",
		"status" => "status",
		"user" => "id_user",
		"date" => "date_creation",
		"expert" => "id_expert"
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

	public function selectAllFilterNotStarted(string $search, string $order, string $sort, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=2 ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%$search%";
		return $this->prepared_query($sql, ["search" => $search]);
	}

	public function selectTotalFilterNotStarted(string $search, string $order, string $sort): int|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=2 ";
		$sql .= "ORDER BY $sort $order ";
		$search = "%" . $search . "%";
		$total = $this->prepared_query($sql, ["search" => $search], unique: true);
		if($total === false){
			return $total;
		}
		return $total['count'];
	}
	public function selectTotalNotStarted(): int|false{
		$sql = "SELECT COUNT(id_offer) as count FROM OFFERS WHERE status=2";
		$res = $this->query($sql, unique: true);
		return $res['count'] ?? false;
	}

	public function selectAllFilterActive(string $search, string $order, string $sort, int $expert, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=3 AND id_expert=:id ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%$search%";
		return $this->prepared_query($sql, ["search" => $search, 'id' => $expert]);
	}

	public function selectTotalFilterActive(string $search, string $order, string $sort, int $expert): int|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=3 AND id_expert=:id ";
		$sql .= "ORDER BY $sort $order ";
		$search = "%" . $search . "%";
		$total = $this->prepared_query($sql, ["search" => $search, 'id' => $expert], unique: true);
		if($total === false){
			return $total;
		}
		return $total['count'];
	}
	public function selectTotalActive(int $expert): int|false{
		$sql = "SELECT COUNT(id_offer) as count FROM OFFERS WHERE status=3 AND id_expert=:id";
		$res = $this->prepared_query($sql, ['id' => $expert], unique: true);
		return $res['count'] ?? false;
	}

	public function selectAllFilterOld(string $search, string $order, string $sort, int $expert, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status>4 AND id_expert=:id ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%$search%";
		return $this->prepared_query($sql, ["search" => $search, 'id' => $expert]);
	}

	public function selectTotalFilterOld(string $search, string $order, string $sort, int $expert): int|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status>4 AND id_expert=:id ";
		$sql .= "ORDER BY $sort $order ";
		$search = "%" . $search . "%";
		$total = $this->prepared_query($sql, ["search" => $search, 'id' => $expert], unique: true);
		if($total === false){
			return $total;
		}
		return $total['count'];
	}
	public function selectTotalOld(int $expert): int|false{
		$sql = "SELECT COUNT(id_offer) as count FROM OFFERS WHERE status>4 AND id_expert=:id";
		$res = $this->prepared_query($sql, ['id' => $expert], unique: true);
		return $res['count'] ?? false;
	}

	public function selectAllFilterReception(string $search, string $order, string $sort, int $iteration = 0): array|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id FROM $this->table_name ";
		$sql .= "WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=1 ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%$search%";
		return $this->prepared_query($sql, ["search" => $search]);
	}

	public function selectTotalFilterReception(string $search, string $order, string $sort): int|false{
		if($sort === 'id'){
			$sort = $this->id_name;
		}else{
			if(key_exists($sort, $this->column)){
				$sort = $this->column[$sort];
			}else{
				return false;
			}
		}
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE (";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ) AND $this->id_name<>0 AND status=1 ";
		$sql .= "ORDER BY $sort $order ";
		$search = "%" . $search . "%";
		$total = $this->prepared_query($sql, ["search" => $search], unique: true);
		if($total === false){
			return $total;
		}
		return $total['count'];
	}
	public function selectTotalReception(): int|false{
		$sql = "SELECT COUNT(id_offer) as count FROM OFFERS WHERE status=1";
		$res = $this->prepared_query($sql, [], unique: true);
		return $res['count'] ?? false;
	}
}