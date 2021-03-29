<?php


use PHPUnit\Framework\TestCase;
use \Hyperion\API\UserModel;

final class UserModelTest extends TestCase{
	private $md;
	public function __construct(?string $name = null, array $data = [], $dataName = '')
	{
		$this->md = new UserModel();
		parent::__construct($name, $data, $dataName);
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
}