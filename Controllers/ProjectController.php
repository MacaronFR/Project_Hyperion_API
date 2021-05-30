<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ProjectController implements Controller{
	private ProjectModel $pm;
	private FilesModel $fm;

	public function __construct(){
		$this->pm = new ProjectModel();
		$this->fm = new FilesModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if($args['additional'][0] === 'popular'){
			$this->getPopular($args);
		}elseif($args['additional'][0] === "latest"){
			$this->getLast($args);
		}else{
			$this->getAll($args);
		}
	}
	public function getAll(array $args){
		if(count($args["uri_args"]) === 0){
			$project = $this->pm->selectAll(limit:false);
			if($project){
				$files = $this->fm->selectWithB64($project['logo']);
				//$files = file_get_contents($files['file_name'],$files['file_path']);
				response(200,"All Projects,$files");
			}else{
				response(204,"No Content");
			}
		}elseif(count($args['uri_args']) === 1){
			if(is_numeric($args['uri_args'][0])){
				$row = (int)$args['uri_args'][0];
				$project = $this->pm->selectAll($row);
				if($project){
					$files = $this->fm->selectWithB64($project['logo']);
					$files = file_get_contents($files['file_name'],$files['file_path']);
					$start = $row * 10 + 1;
					$end = ($row + 1) * 10;
					response(200,"Project $start to $end, $project,$files");
				}else{
					response(204,"No Content");
				}
			}else{
				response(400,"Bad Request");
			}
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
				$project = $this->pm->selectPopular($row);
				if($project){
					$files = $this->fm->selectWithB64($project['logo']);
					//$files = file_get_contents($files['file_name'],$files['file_path']);
					response(200,"Most Popular Project,$files");
				}else{
					response(204,"No Content");
				}
			}else{
				response(400,"Bad Request");
			}
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
		}else{
			$search = $args['uri_args'][1];
			$order = strtoupper($args['uri_args'][2] ?? "ASC");
			if($order !== "ASC" && $order !== "DESC"){
				response(409,"Bad Request");
			}
			$sort = $args['uri_args'][3] ?? "id";
			$project = $this->pm->selectAllFilter($search,$order,$sort);
			if($project){
				$files = $this->fm->selectWithB64($project['logo']);
				//$files = file_get_contents($files['file_name'],$files['file_path']);
				response(200,"Last Project,$files");
			}else{
				response(204,"No Content");
			}
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