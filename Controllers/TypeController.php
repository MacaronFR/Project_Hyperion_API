<?php


namespace Hyperion\API;


class TypeController implements Controller{
	private TypeModel $tym;

	public function __construct(){
		$this->tym = new TypeModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(isset($args['uri_args'][0])){
			if(is_numeric($args['uri_args'][0])){
				$iteration = $args['uri_args'][0];
			}else{
				response(400, "Bad Request");
			}
		}else{
			$iteration = 0;
		}
		$types = $this->tym->selectAll($iteration);
		if($types === false){
			response(500, "Internal Server Error");
		}
		if(empty($types)){
			response(204, "No Content");
		}
		response(200, "Types", $types);
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		if(checkToken($args['uri_args'][0], 3)) if(isset($args['post_args']['name']) && count($args['post_args']) === 1){
			if($this->tym->selectByName($args['post_args']['name']) === false){
				if($this->tym->insert(['name' => $args['post_args']['name']])){
					response(201, "Type has been created");
				}else{
					response(402, "Type Already Exist");
				}
			}else{
				response(400, "Bad Requests");
			}
		}else{
			response(403, "Forbidden");
		}
	}

	public function put(array $args){
		// TODO: Implement put() method.
	}

	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}