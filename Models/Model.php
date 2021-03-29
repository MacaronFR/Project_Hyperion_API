<?php


namespace Hyperion\API;
use PDO;
use PDOStatement;

abstract class Model{
	/** @var PDO $bdd PDO object to database */
	private PDO $bdd;
	/**
	 * Models constructor.
	 */
	public function __construct(){
		require "conf.php";
		$dbname = $db["dbname"];
		$host = $db["host"];
		$user = $db["user"];
		$passwd = $db["passwd"];
		$port = $db["port"];
		$this->bdd = new PDO("mysql:dbname=${dbname};host=${host}:${port}", $user, $passwd);
	}
	/**
	 * Query to Database
	 * @param string $statement Statement to query
	 * @return PDOStatement
	 */
	protected function query(string $statement): PDOStatement{
		return $this->bdd->query($statement);
	}
	/**
	 * @param string $statement Statement to prepare and query
	 * @param array $param Array of param of form $param["param_name"] = $param_value
	 * @param bool $unique If the query return only one row
	 * @return array|false
	 */
	protected function prepared_query(string $statement, array $param, bool $unique = false): array|false{
		$req = $this->bdd->prepare($statement);
		foreach($param as $name => $value){
			$req->bindParam($name, $value);
		}
		$req->execute();
		if($unique)
			return $req->fetch(PDO::FETCH_ASSOC);
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	abstract public function selectAll();
	abstract public function select(int $id);
	abstract public function update(int $id, array $value);
	abstract public function insert(array $value);
	abstract public function delete(int $id);
}