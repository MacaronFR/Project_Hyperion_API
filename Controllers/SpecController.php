<?php


namespace Hyperion\API;


class SpecController implements Controller{
	private TokenModel $tm;
	private SpecificationModel $sm;

	#[Pure] public function __construct(){
		$this->sm = new SpecificationModel();
		$this->tm = new TokenModel();
	}

	public function get(array $args){
		// TODO: Implement get() method.
	}
	public function post(array $args){
		// TODO: Implement post() method.
	}
	public function put(array $args){
		// TODO: Implement put() method.
	}
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}