<?php


namespace Hyperion\API;


class CategoryController extends Controller{
	private CategoryModel $cm;
	public function __construct(){
		$this->cm = new CategoryModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
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
	public function post(array $args){
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
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		var_dump($args);
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}