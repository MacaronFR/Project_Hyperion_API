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
		if(count($args['uri_args']) >= 1){
			if(is_numeric($args['uri_args'][0])){
				$page = (int)$args['uri_args'][0];
			}else{
				response(400, "Bad Request");
			}
		}
		if(count($args['uri_args']) > 1){
			$order = $args['uri_args'][2] ?? 'ASC';
			$order = strtoupper($order);
			if($order !== "ASC" && $order !== "DESC"){
				response(409, "Bad Request");
			}
			$search = $args['uri_args'][1];
			$sort = $args['uri_args'][3] ?? 'id';
			$result = $this->cm->selectAllFilter($search, $order, $sort, $page);
			$totalFilter = $this->cm->selectTotalFilter($search, $order, $sort);
			$total = $this->cm->selectTotal();
		}else{
			if(count($args['uri_args']) === 0){
				$result = $this->cm->selectAll(limit: false);
			}else{
				$result = $this->cm->selectAll($page);
			}
			$total = $this->cm->selectTotal();
			$totalFilter = $total;
		}
		if($result === false){
			response(500, "Error while retrieving data");
		}
		if(empty($result)){
			response(204, "No content");
		}
		if($total === false || $totalFilter === false){
			response(500, "Internal Server Error");
		}
		$start = $page * 10 + 1;
		if(count($args['uri_args']) === 0){
			$end = $totalFilter;
		}else{
			$end = ($page + 1) * 10;
		}
		$result['total'] = $totalFilter;
		$result['totalNotFiltered'] = $total;
		response(200, "Category $start to $end", $result);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!isset($args['post_args']['name'])){
			response(400, "Bad Request");
		}
		if(empty($args['post_args']['name'])){
			response(400, "Bad Request");
		}
		if($this->cm->selectByName($args['post_args']['name']) !== false){
			response(202, "Category Already exist");
		}
		$res = $this->cm->insert(['name' => $args['post_args']['name']]);
		if($res === false){
			response(500, "Error while creating category");
		}
		response(201, "Category created", [$res]);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!isset($args['put_args']['name'])){
			response(400, "Bad Request");
		}
		if(empty($args['put_args']['name'])){
			response(400, "Bad Request");
		}
		$cat = $this->cm->select($args['uri_args'][1]);
		if($cat === false){
			response(500, "Internal Server Error");
		}
		if($this->cm->selectByName($args['put_args']['name'])){
			response(202, "Category name already exist");
		}
		if($cat['name'] === $args['put_args']['name']){
			response(204, "No change");
		}
		if($this->cm->update($args['uri_args'][1], $args['put_args'])){
			response(200, "Category Updated");
		}else{
			response(500, "Error while updating");
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