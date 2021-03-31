<?php


use Hyperion\API\TokenModel;
use PHPUnit\Framework\TestCase;

class TokenModelTest extends TestCase{
	private TokenModel $tm;
	public function __construct(?string $name = null, array $data = [], $dataName = ''){
		parent::__construct($name, $data, $dataName);
		$this->tm = new TokenModel();
	}
	protected function getMethod(string $name): ReflectionMethod|false {
		$class = new ReflectionClass($this->tm);
		try {
			$method = $class->getMethod($name);
		} catch (ReflectionException $e) {
			echo "Error : " . $e->getMessage();
			return false;
		}
		$method->setAccessible(true);
		return $method;
	}
	public function testPrepareColumnAndParameter(){
		$method = $this->getMethod("prepare_column_and_parameter");
		$this->assertIsArray($method->invokeArgs($this->tm, ["scope"]));
		$this->assertIsArray($method->invokeArgs($this->tm, ["client"]));
		$this->assertIsArray($method->invokeArgs($this->tm, ["token"]));
		$this->assertIsArray($method->invokeArgs($this->tm, ["end"]));
		$this->assertIsArray($method->invokeArgs($this->tm, ["user"]));
		$this->assertFalse($method->invokeArgs($this->tm, ["nikktoi"]));
	}
	public function testSelectAll(){
		$this->assertFalse($this->tm->selectAll());
	}
	public function testSelectByClient(){
		$this->assertIsArray($this->tm->selectByClient(1));
		$this->assertFalse($this->tm->selectByClient(1234));
	}
	public function testSelectByUser(){
		$this->assertIsArray($this->tm->selectByUser(1));
		$this->assertFalse($this->tm->selectByUser(1234));
	}
	public function testUpdate(){
		$id = $this->tm->selectByUser(1);
		$now = new DateTime();
		$now->add(new DateInterval("PT2H"));
		$this->assertIsArray($id);
		$this->assertTrue($this->tm->update($id['id_token'], ["end" => $now->format("Y-m-d H:i:s")]));
		$this->assertFalse($this->tm->update($id['id_token'], ["lol" => $now->format("Y-m-d H:i:s")]));
	}
	public function testSelect(){
		$id = $this->tm->selectByUser(1);
		$this->assertIsArray($id);
		$this->assertIsArray($this->tm->select((int)$id['id_token']));
	}
	public function testSelectByToken(){
		$id = $this->tm->selectByUser(1);
		$this->assertIsArray($id);
		$this->assertIsArray($this->tm->selectByToken($id['value']));
	}
}