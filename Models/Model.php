<?php


namespace Hyperion\API;
use PDO;
use PDOException;

abstract class Model{
	/** @var PDO $bdd PDO object to database */
	protected PDO $bdd;
	protected const INSERT = 1;
	protected const UPDATE = 2;
	protected string $table_name;
	protected string $id_name;
	protected array $column;
	protected int $max_row = 500;
	/**
	 * Models constructor.
	 * @codeCoverageIgnore
	 */
	public function __construct(){
		require "conf.php";
		$dbname = $db["dbname"];
		$host = $db["host"];
		$user = $db["user"];
		$passwd = $db["passwd"];
		$port = $db["port"];
		$this->bdd = new PDO("mysql:dbname=${dbname};host=${host}:${port}", $user, $passwd);
		$this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	/**
	 * Query to Database
	 * @param string $statement Statement to query
	 * @return false|array
	 */
	protected function query(string $statement): false|array{
		try {
			$res = $this->bdd->query($statement);
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException){
			return false;
		}
	}
	/**
	 * @param string $statement Statement to prepare and query
	 * @param array $param Array of param of form $param["param_name"] = $param_value
	 * @param bool $unique If the query return only one row
	 * @param bool $fetch Query have to be fetch ?
	 * @return array|bool
	 */
	protected function prepared_query(string $statement, array $param, bool $unique = false, bool $fetch = true, bool $last_id = false): array|bool|int{
		$req = $this->bdd->prepare($statement);
		foreach($param as $name => $value){
			$req->bindValue($name, $value);
		}
		try {
			$req->execute();
			if($fetch) {
				if ($unique)
					return $req->fetch(PDO::FETCH_ASSOC);
				return $req->fetchAll(PDO::FETCH_ASSOC);
			}else{
				if($last_id)
					return $this->bdd->lastInsertId();
				return $req->rowCount() !== 0;
			}
		}catch (PDOException $e){
			echo "Error : ".$e->getMessage();
			return false;
		}
	}

	protected function prepare_query_string(array $fields, int $type): string|false{
		return match($type){
			self::INSERT => $this->prepare_insert_query($fields),
			self::UPDATE => $this->prepare_update_query($fields),
			default => false
		};
	}

	protected function prepare_insert_query(array $fields): string|false{
		$query = "INSERT INTO " . $this->table_name . " ";
		$column = "(";
		$param = "(";
		foreach($fields as $name => $value){
			$column_name = $this->column[$name];
			$column .= $column_name . ", ";
			$param .= ":" . $name . ", ";
		}
		$column = substr($column, 0, -2);
		$param = substr($param, 0, -2);
		$query .= $column . ") VALUES " . "$param" . ");";
		return $query;
	}
	protected function prepare_update_query(array $fields): string|false{
		$query = "UPDATE " . $this->table_name . " SET ";
		$arg = "";
		foreach($fields as $name => $value){
			$column_name = $this->column[$name];
			$arg .= $column_name . "=:" . $name . ", ";
		}
		$arg = substr($arg, 0, -2);
		$query .= "$arg" . " WHERE " . $this->id_name . "=:id;";
		return $query;
	}

	public function selectAll(int $iteration = 0): array|false{
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		foreach($this->column as $key => $item){
			$sql .= " $item as $key,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name LIMIT $start, $this->max_row";
		return $this->query($sql);
	}

	public function selectTotal(): int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name";
		$res = $this->query($sql);
		if($res !== false){
			return (int)$res[0]['count'];
		}
		return false;
	}

	public function selectAllFilter(string $search, string $order, string $sort, int $iteration = 0): array|false{
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
		$sql .= "WHERE ";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%" . $search . "%";
		return $this->prepared_query($sql, ["search" => $search]);
	}

	public function selectTotalFilter(string $search, string $order, string $sort, int $iteration = 0): int|false{
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
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE ";
		foreach($this->column as $item){
			$sql .= "$item LIKE :search OR ";
		}
		$sql .= "$this->id_name LIKE :search ";
		$sql .= "ORDER BY $sort $order ";
		$sql .= "LIMIT $start, $this->max_row;";
		$search = "%" . $search . "%";
		$total = $this->prepared_query($sql, ["search" => $search], unique: true);
		if($total === false){
			return $total;
		}
		return $total['count'];
	}

	public function select(int $id): array|false{
		$sql = "SELECT";
		foreach($this->column as $name => $item){
			$sql .= " $item as $name,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE $this->id_name=:id ";
		return $this->prepared_query($sql, ["id" => $id], unique: true);
	}
	public function update(int $id, array $value): bool{
		$sql = $this->prepare_update_query($value);
		$value["id"] = $id;
		return $this->prepared_query($sql, $value, fetch: false);
	}
	public function insert(array $value): int|false{
		$sql = $this->prepare_insert_query($value);
		return $this->prepared_query($sql, $value, fetch: false, last_id: true);
	}
	public function delete(int $id): bool{
		$sql = "DELETE FROM $this->table_name WHERE $this->id_name=:id";
		return  $this->prepared_query($sql, ["id" => $id], fetch: false);
	}
}