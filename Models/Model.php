<?php


namespace Hyperion\API;
use PDO;
use PDOStatement;
use PDOException;

abstract class Model{
	/** @var PDO $bdd PDO object to database */
	protected PDO $bdd;
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
		$this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	/**
	 * Query to Database
	 * @param string $statement Statement to query
	 * @return false|PDOStatement
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
	 * @param bool$fetch Query have to be fetch ?
	 * @return array|bool
	 */
	protected function prepared_query(string $statement, array $param, bool $unique = false, bool $fetch = true): array|bool{
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
			}else
				return $req->rowCount() !== 0;
		}catch (PDOException $e){
			echo "Error : ".$e->getMessage();
			return false;
		}
	}
	abstract public function selectAll(int $iteration): array|false;
	abstract public function select(int $id): array|false;
	abstract public function update(int $id, array $value): bool;
	abstract public function insert(array $value): bool;
	abstract public function delete(int $id): bool;
}