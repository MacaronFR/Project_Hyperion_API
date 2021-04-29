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
		if(coount($args['uri_args']) === 1 && is_numeric($args['uri_args'][1])){
			$iteration = (int)$args['uri_args'][1];
			$this->sm->select($iteration);
		}else{
			response(400,"Bad Request");
		}
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