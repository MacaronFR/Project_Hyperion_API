<?php


use PHPUnit\Framework\TestCase;
use \Hyperion\API\UserModel;

final class UserModelTest extends TestCase{
	private UserModel $md;
	public function __construct(?string $name = null, array $data = [], $dataName = ''){
		$this->md = new UserModel();
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * Get private and protected method
	 * @param string $name Method name
	 * @return ReflectionMethod|false
	 */
	protected function getMethod(string $name): ReflectionMethod|false {
		$class = new ReflectionClass($this->md);
		try {
			$method = $class->getMethod($name);
		} catch (ReflectionException $e) {
			echo "Error : " . $e->getMessage();
			return false;
		}
		$method->setAccessible(true);
		return $method;
	}
	public function testCanBeCreated(){
		$this->assertInstanceOf(UserModel::class, $this->md);
	}
	public function testCanSelectValidID(){
		$this->assertNotFalse($this->md->select("1"));
	}
	public function testCantSelectInvalidID(){
		$this->assertFalse($this->md->select("1234"));
	}
	public function testSelectAll(){
		$this->assertNotFalse($this->md->selectAll());
	}
	public function testSelectAllIteration(){
		$this->assertNotFalse($this->md->selectAll(1));
	}
	public function testValidQuery(){
		$query = $this->getMethod("query");
		$this->assertNotFalse($query->invokeArgs($this->md, ["SELECT * FROM USERS LIMIT 1"]));
	}
	public function testInvalidQuery(){
		$query = $this->getMethod("query");
		$this->assertFalse($query->invokeArgs($this->md, ["CREATE TABLE TEST(id INT PRIMARY KEY );"]));
		$this->assertEmpty($query->invokeArgs($this->md, ["SELECT * FROM USERS WHERE id_user=1234"]));
	}
	public function testInvalidPrivilegesQuery(){
		$query = $this->getMethod("query");
		$this->assertEmpty($query->invokeArgs($this->md, ["SELECT * FROM mysql.user"]));
	}
	public function testValidPrepared(){
		$prepared = $this->getMethod("prepared_query");
		$this->assertNotFalse($prepared->invokeArgs($this->md, ["SELECT * FROM USERS WHERE id_user=:id", ["id"=>1]]));
	}
	public function testInvalidPrepared(){
		$prepared = $this->getMethod("prepared_query");
		$this->assertFalse($prepared->invokeArgs($this->md, ["CREATE TABLE TEST(id INT PRIMARY KEY)", []]));
	}
	public function testValidUpdate(){
		$date = new DateTime();
		$value = [
			'name' => "TURBIEZ",
			'fname' => "Denis",
			'gc' => 0,
			'type' => 0,
			'mail' => "denisft77@gmail.com",
			'llog' => $date->format("Y-m-d h:m:s"),
			'ac_creation' => "2021-02-12",
			'addr' => 1,
			'passwd' => '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b'
		];
		$this->assertTrue($this->md->update("1", $value));
	}
	public function testInvalidUpdate(){
		$value = [
			'name' => "TURBIEZ",
			'firstname' => "Denis",
			'green_coins' => 0,
			'type' => 0,
			'mail' => "denisft77@gmail.com",
			'last_login' => "2021-03-29 11:12:13",
			'account_creation' => "2021-02-12",
			'address' => 1
		];
		$value2 = [
			'name' => "TURBIEZ",
			'firstname' => "Denis",
			'green_coins' => 0,
			'type' => 0,
			'last_login' => "2021-03-29 11:12:13",
			'account_creation' => "2021-02-12",
			'address' => 1
		];
		$this->assertFalse($this->md->update("1234", $value));
		$this->assertFalse($this->md->update("1234", $value2));
	}
	public function testValidInsert(){
		$date = new DateTime();
		$value = [
			'name' => "TURBIEZ",
			'fname' => "Denis",
			'gc' => 0,
			'type' => 0,
			'mail' => "denisft77@gmail.com".bin2hex(random_bytes(3)),
			'llog' => $date->format("Y-m-d h:m:s"),
			'ac_creation' => "2021-02-12",
			'addr' => 1,
			'passwd' => '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b'
		];
		$this->assertTrue($this->md->insert($value));
	}
	public function testInvalidInsert(){
		$value = [
			'name' => "TURBIEZ",
			'firstname' => "Denis",
			'green_coins' => 0,
			'type' => 0,
			'last_login' => "2021-03-29 11:12:13",
			'account_creation' => "2021-02-12",
			'address' => 1
		];
		$this->assertFalse($this->md->insert($value));
	}

	public function testParamColum(){
		$function = $this->getMethod("prepare_column_and_parameter");
		$this->assertIsArray($function->invokeArgs($this->md, ['fname']));
		$this->assertFalse($function->invokeArgs($this->md, ['nikk']));
	}

	public function testPrepareUpdateQuery(){
		$function = $this->getMethod("prepare_update_query");
		$this->assertNotFalse($function->invokeArgs($this->md, [['addr' => 1, 'gc' => 1] ]));
	}
	public function testSelectFromMail(){
		$this->assertIsArray($this->md->selectFromMail("denisft77@gmail.com"));
		$this->assertFalse($this->md->selectFromMail("NIK TOI"));
	}
	//public function testDeleteValidID(){
		//$this->assertTrue($this->md->delete(11));
	//}
//	public function testDeleteInvalidID(){
//		$this->assertFalse($this->md->delete(12));
//	}
}