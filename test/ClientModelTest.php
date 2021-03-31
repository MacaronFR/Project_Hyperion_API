<?php


use Hyperion\API\ClientModel;
use PHPUnit\Framework\TestCase;

class ClientModelTest extends TestCase{
	private ClientModel $cm;
	function __construct(?string $name = null, array $data = [], $dataName = ''){
		parent::__construct($name, $data, $dataName);
		$this->cm = new ClientModel();
	}
	protected function getMethod(string $name): ReflectionMethod|false {
		$class = new ReflectionClass($this->cm);
		try {
			$method = $class->getMethod($name);
		} catch (ReflectionException $e) {
			echo "Error : " . $e->getMessage();
			return false;
		}
		$method->setAccessible(true);
		return $method;
	}
	public function testSelectFromID(){
		$this->assertIsArray($this->cm->selectFromClientID("1234"));
		$this->assertFalse($this->cm->selectFromClientID("LOL"));
	}
	public function testPrepareColumnAndParameter(){
		$method = $this->getMethod("prepare_column_and_parameter");
		$this->assertIsArray($method->invokeArgs($this->cm, ["scope"]));
		$this->assertIsArray($method->invokeArgs($this->cm, ["client"]));
		$this->assertIsArray($method->invokeArgs($this->cm, ["client_secret"]));
		$this->assertIsArray($method->invokeArgs($this->cm, ["name"]));
		$this->assertIsArray($method->invokeArgs($this->cm, ["user"]));
		$this->assertFalse($method->invokeArgs($this->cm, ["nikktoi"]));
	}
}