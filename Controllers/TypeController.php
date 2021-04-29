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
			$result = $this->tm->selectAllFilter($search, $order, $sort, $page);
			$totalFilter = $this->tm->selectTotalFilter($search, $order, $sort);
			$total = $this->tm->selectTotal();
		}else{
			if(count($args['uri_args']) === 0){
				$result = $this->tm->selectAll(limit: false);
			}else{
				$result = $this->tm->selectAll($page);
			}
			$total = $this->tm->selectTotal();
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
		if(isset($args['additional'][0]) && $args['additional'][0] === 'cat'){
			foreach($result as &$res){
				$cat_name = $this->cm->select($res['category']);
				if($cat_name === false){
					response(500, "Internal Server Error");
				}
				$res['category_id'] = $res['category'];
				$res['category'] = $cat_name['name'];
			}
		}
		$result['total'] = $totalFilter;
		$result['totalNotFiltered'] = $total;
		response(200, "Category $start to $end", $result);
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