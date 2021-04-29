<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class TypeController implements Controller{
	private TypeModel $tm;
	private CategoryModel $cm;
	private ReferenceModel $rm;

	public function __construct(){
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
		$this->rm = new ReferenceModel();
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
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad request");
		}
		if(empty($args['put_args'])){
			response(400, "Bad Request");
		}
		$type = $this->tm->select($args['uri_args'][1]);
		if($type === false){
			response(404, "Not Found");
		}
		$new_field = array_intersect_key($args['put_args'], $type);
		if(empty($new_field)){
			response(400, "Bad Request");
		}
		if(isset($new_field['type'])){
			$exist = $this->tm->selectByType($new_field['type']);
			if($exist !== false){
				response(202, "Type exist");
			}
		}
		foreach($new_field as $key => $f){
			if((string)$f === $type[$key]){
				unset($new_field[$key]);
			}
		}
		if(empty($new_field)){
			response(204, "No Update");
		}
		if(isset($new_field['category'])){
			$cat = $this->cm->select($new_field['category']);
			if($cat === false){
				response(404, "Category not found");
			}
		}
		$res = $this->tm->update($args['uri_args'][1], $new_field);
		if($res === false){
			response(500, "Internal Server Error");
		}else{
			response(200, "Updated");
		}
	}

	public function delete(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad request");
		}
		$type = $this->tm->select($args['uri_args'][1]);
		if($type === false){
			response(404, "Not Found");
		}
		$ref = $this->rm->selectAllByType($args['uri_args'][1]);
		if($ref === false){
			response(500, "Internal Server Error");
		}
		if(!empty($ref)){
			response(209, "Conflict");
		}
		$res = $this->tm->delete($args['uri_args'][1]);
		if($res === false){
			response(500, "Internal Server Error");
		}
		response(204, "Type Deleted");
	}
}