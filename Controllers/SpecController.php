<?php


namespace Hyperion\API;


class SpecController implements Controller{
	private TokenModel $tm;
	private SpecificationModel $sm;

	 public function __construct(){
		$this->sm = new SpecificationModel();
		$this->tm = new TokenModel();
	}

	public function get(array $args){
		if(count($args['uri_args']) === 1 && is_numeric($args['uri_args'][1])){
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
		if(!checkToken($args['uri_args'][0],3)){
			response(403, "Forbidden");
		}
		if(!is_numeric($args['uri_args'][1])){
			response(400,"Bad Request");
		}
		if(empty($args['put_args'])){
			response(400,"Bad Request");
		}
		$spec = $this->sm->select($args['uri_args'][1]);
		if($spec === false){
			response(404,"Not Found");
		}
		$new_spec = array_intersect_key($args['put_args'],$spec);
		if(empty($new_spec)){
			response(400,"Bad Request");
		}

		foreach($new_spec as $key => $val){
			if((string)$val === $spec[$key]){
				unset($new_spec[$key]);
			}
		}
		if(empty($new_spec)){
			response(204,"No Update");
		}
		$result = $this->sm->update($args['uri_args'][1], $new_spec);
		if($result === false){
			response(500,"Internal Server Error");
		}
		response(201,"Updated");

	}
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}