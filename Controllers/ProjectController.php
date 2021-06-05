<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\ArrayShape;
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
		}elseif($args['additional'][0] === "all"){
			$this->getAll($args);
		}elseif($args['additional'][0] === "logo"){
			$this->getLogo($args);
		}
	}

	#[NoReturn] public function getAll(array $args){
		if(count($args["uri_args"]) === 0){
			$projects = $this->pm->selectAllValid(limit: false);
		}elseif(count($args['uri_args']) >= 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			if(count($args['uri_args']) > 1){
				$order = match($args['uri_args'][2]){@
					'DESC' => 'DESC',
					default => 'ASC'
				};
				$sort = match ($args['uri_args'][3]){
					'contribution' => 'contribution',
					'name' => 'name',
					'RNA' => 'RNA',
					default => 'id'
				};
				$search = $args['uri_args'][1];
				$projects = $this->pm->selectAllValidFilter($search, $order, $sort, $args['uri_args'][0]);
			}else{
				$projects = $this->pm->selectAllValid($args['uri_args'][0]);
			}
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

	#[NoReturn] private function getLogo(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$project = $this->pm->select($args['uri_args'][0]);
		if($project === false){
			response(404, "Not Found");
		}
		$logo = $this->logoRetrieve($project['logo']);
		response(200, "Project Logo", ['content' => $logo['content'], 'id' => $project['logo']]);
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
				$p['logo'] = $this->logoRetrieve($p['logo']);
			}
		}
	}

	#[ArrayShape(['file_name' => "string", 'content' => "string"])] private function logoRetrieve(int $id): array{
		$files = $this->fm->selectWithB64($id);
		if($files === false){
			response(500, "Internal Server Error");
		}
		return ['file_name' => $files['file_name'], 'content' => $files['content']];
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