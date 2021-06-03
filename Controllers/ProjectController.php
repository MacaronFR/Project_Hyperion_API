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

	#[NoReturn] public function getAll(array $args){
		if(count($args["uri_args"]) === 0){
			$projects = $this->pm->selectAllValid(limit: false);
		}elseif(count($args['uri_args']) === 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			$projects = $this->pm->selectAllValid($args['uri_args'][0]);
		}
		$this->processProject($projects, !(isset($args['additional'][1]) && $args['additional'][1] === 'nologo'));
		response(200, "Projects", $projects);
	}

	#[NoReturn] public function getPopular(array $args){
		if(count($args["uri_args"]) === 0){
			$projects = $this->pm->selectPopular(limit: false);
		}elseif(count($args['uri_args']) === 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			$projects = $this->pm->selectPopular($args['uri_args'][0]);
		}
		$this->processProject($projects, !(isset($args['additional'][1]) && $args['additional'][1] === 'nologo'));
		response(200, "Popular Project", $projects);
	}

	#[NoReturn] public function getLast(array $args){
		if(count($args["uri_args"]) === 0){
			$projects = $this->pm->selectAllValidLast(limit: false);
		}else{
			$projects = $this->pm->selectAllValidLast($args['uri_args'][0]);
		}
		$this->processProject($projects, !(isset($args['additional'][1]) && $args['additional'][1] === 'nologo'));
		response(200, "Latest Project", $projects);
	}

	private function processProject(array|false &$projects, bool $logo){
		if($projects === false){
			response(500, "Internal Server Error");
		}
		if(empty($projects)){
			response(204, "No Content");
		}
		if($logo){
			foreach($projects as &$p){
				$files = $this->fm->selectWithB64($p['logo']);
				if($files === false){
					response(500, "Internal Server Error");
				}
				$p['logo'] = ['file_name' => $files['file_name'], 'content' => $files['content']];
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