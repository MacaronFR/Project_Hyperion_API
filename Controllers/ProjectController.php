<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ProjectController implements Controller{
	private ProjectModel $pm;

	public function __construct(){
		$this->pm = new ProjectModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(count($args["uri_args"]) === 0){
			$project = $this->pm->selectAll(limit:false);
			if($project){
				response(200,"All Projects");
			}else{
				response(204,"No Content");
			}
		}elseif(count($args['uri_args']) === 1){
			if(is_numeric($args['uri_args'][0])){
				$row = (int)$args['uri_args'][0];
			}else{
				response(400,"Bad Request");
			}
		}
		$project = $this->pm->selectAll($row);
		if($project){
			$start = $row * 10 + 1;
			$end = ($row + 1) * 10;
			response(200,"Project $start to $end, $project");
		}else{
			response(204,"No Content");
		}
	}

	public function getPopular(array $args){
		if(count($args["uri_args"]) === 0){
		$project = $this->pm->selectPopular(limit:false);
		if($project){
			response(200,"All Projects");
		}else{
			response(204,"No Content");
		}
	}elseif(count($args['uri_args']) === 1){
			if(is_numeric($args['uri_args'][0])){
				$row = (int)$args['uri_args'][0];
			}else{
				response(400,"Bad Request");
			}
		}
		$project = $this->pm->selectPopular($row);
		if($project){
			response(200,"Most Popular Project");
		}else{
			response(204,"No Content");
		}
	}
	public function getLast(array $args){
		if(count($args["uri_args"]) === 0){
			$project = $this->pm->selectPopular(limit: false);
			if($project){
				response(200, "All Projects");
			}else{
				response(204, "No Content");
			}
		}elseif(count($args['uri_args']) === 1){
			$row = (int)$args['uri_args'][0];
		}else{
			response(400,"Bad Request");
		}
		$search = $args['uri_args'][1];
		$order = strtoupper($args['uri_args'][2] ?? "ASC");
		if($order !== "ASC" && $order !== "DESC"){
			response(409,"Bad Request");
		}
		$sort = $args['uri_args'][3] ?? "id";
		$project = $this->pm->selectAllFilter($search,$order,$sort);
		if($project){
			response(200,"Last Project");
		}else{
			response(204,"No Content");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		// TODO: Implement post() method.
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}