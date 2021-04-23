<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class TypeController implements Controller{
	private TypeModel $tm;
	private CategoryModel $cm;

	public function __construct(){
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if(isset($args['uri_args'][0])){
			if(is_numeric($args['uri_args'][0])){
				$iteration = $args['uri_args'][0];
			}else{
				response(400, "Bad Request");
			}
		}else{
			$iteration = 0;
		}
		$types = $this->tm->selectAll($iteration);
		if($types === false){
			response(500, "Internal Server Error");
		}
		if(empty($types)){
			response(204, "No Content");
		}
		if(isset($args['additional'][0]) && $args['additional'][0] === "cat"){
			foreach($types as &$type){
				$cat = $this->cm->select($type['category']);
				if($cat === false){
					response(500, "Internal Server Error");
				}
				$type['category_name'] = $cat['name'];
			}
		}
		response(200, "Types", $types);
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		if(checkToken($args['uri_args'][0], 3)) if(isset($args['post_args']['name']) && count($args['post_args']) === 1){
			if($this->tm->selectByName($args['post_args']['name']) === false){
				if($this->tm->insert(['name' => $args['post_args']['name']])){
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