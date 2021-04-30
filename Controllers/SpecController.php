<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";

class SpecController implements Controller{
	private TokenModel $tm;
	private SpecificationModel $sm;

	public function __construct(){
		$this->tm = new TokenModel();
		$this->sm = new SpecificationModel();
	}

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
			$result = $this->sm->selectAllFilter($search, $order, $sort, $page);
			$totalFilter = $this->sm->selectTotalFilter($search, $order, $sort);
			$total = $this->sm->selectTotal();
		}else{
			if(count($args['uri_args']) === 0){
				$result = $this->sm->selectAll(limit: false);
			}else{
				$result = $this->sm->selectAll($page);
			}
			$total = $this->sm->selectTotal();
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