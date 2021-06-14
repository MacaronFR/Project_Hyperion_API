<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class StateController implements Controller{

	private StateModel $sm;

	public function __construct(){
		$this->sm = new StateModel();
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		$states = $this->sm->selectAll();
		if($states === false){
			response(500, "Internal Server Error");
		}
		response(200, "States", $states);
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		// TODO: Implement post() method.
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}