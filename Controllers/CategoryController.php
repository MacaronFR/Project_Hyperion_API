<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class CategoryController implements Controller{
	private CategoryModel $cm;
	public function __construct(){
		$this->cm = new CategoryModel();
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		$page = 0;
		if(count($args['uri_args']) === 1){
			if(is_numeric($args['uri_args'][0])){
				$page = (int)$args['uri_args'][0];
			}else{
				response(400, "Bad Request");
			}
		}
		$result = $this->cm->selectAll($page);
		$start = $page * 500 + 1;
		$end = ($page + 1) * 500;
		if($result !== false){
			if(empty($result)){
				response(204, "No content");
			}else{
				response(200, "Category $start to $end", $result);
			}
		}else{
			response(500, "Error while retrieving data");
		}

	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(checkToken($args['uri_args'][0], 3)){
			if(count($args['post_args']) === 1 && isset($args['post_args']['name'])){
				if($this->cm->selectByName($args['post_args']['name']) === false){
					if($this->cm->insert(['name' => $args['post_args']['name']])){
						response(201, "Category created");
					}else{
						response(500, "Error while creating category");
					}
				}else{
					response(202, "Category Already exist");
				}
			}else{
				response(400, "Bad Request");
			}
		}else{
			response(403, "Forbidden");
		}
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		if(checkToken($args['uri_args'][0], 3)){
			if(count($args['put_args']) === 1 && isset($args['put_args']['name'])){
				$cat = $this->cm->select($args['uri_args'][1]);
				if($cat !== false){
					if($cat['name'] !== $args['put_args']['name']){
						if($this->cm->update($args['uri_args'][1], $args['put_args'])){
							response(200, "Category Updated");
						}else{
							response(500, "Error while updating");
						}
					}else{
						response(204, "No change");
					}
				}else{
					response(400, "Bad Request, Invalid category ID");
				}
			}else{
				response(400, "Bad Request");
			}
		}else{
			response(403, "Forbidden");
		}
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function delete(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad request");
		}
		$cat = $this->cm->select($args['uri_args'][1]);
		if($cat === false){
			response(500, "Internal server Error");
		}
		if(empty($cat)){
			response(404, "Not found");
		}
		$tm = new TypeModel();
		$types = $tm->selectByCategory($args['uri_args'][1]);
		if($types === false){
			response(500, "Internal Server Error");
		}
		if(!empty($types)){
			response(409, "Types link to Category");
		}
		if($this->cm->delete($args['uri_args'][1])){
			response(204, "No content");
		}else{
			response(500, "Internal Server Error");
		}
	}
}