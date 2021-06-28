<?php


namespace Hyperion\API;


use Cassandra\Date;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;

class ProjectController implements Controller{
	private ProjectModel $pm;
	private FilesModel $fm;
	private TokenModel $tm;

	public function __construct(){
		$this->pm = new ProjectModel();
		$this->fm = new FilesModel();
		$this->tm = new TokenModel();
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
		}elseif($args['additional'][0] === "invalid"){
			$this->getInvalid($args);
		}
	}

	#[NoReturn] public function getInvalid(array $args){
		if(count($args["uri_args"]) === 0){
			$invalid = $this->pm->selectAllProject(limit:false,valid: 0);
		}elseif(count($args['uri_args']) >= 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400,"Bad Request");
			}
			if(count($args['uri_args']) > 1){
				$order = match($args['uri_args'][2]){
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
				$invalid = $this->pm->selectAllProjectFilter($search, $order, $sort, $args['uri_args'][0],0);
			}else{
				$invalid = $this->pm->selectAllProject($args['uri_args'][0], valid:0);
			}
		}
		$this->processProject($invalid, !(isset($args['additional'][1]) && $args['additional'][1] === 'nologo'));
		response(200, "Projects", $invalid);
		}


	#[NoReturn] public function getAll(array $args){
		if(count($args["uri_args"]) === 0){
			$projects = $this->pm->selectAllProject(limit: false);
		}elseif(count($args['uri_args']) >= 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			if(count($args['uri_args']) > 1){
				$order = match($args['uri_args'][2] ?? ""){
					'DESC' => 'DESC',
					default => 'ASC'
				};
				$sort = match ($args['uri_args'][3] ?? ""){
					'contribution' => 'contribution',
					'name' => 'name',
					'RNA' => 'RNA',
					default => 'id'
				};
				$search = $args['uri_args'][1];
				$projects = $this->pm->selectAllProjectFilter($search, $order, $sort, $args['uri_args'][0]);
			}else{
				$projects = $this->pm->selectAllProject($args['uri_args'][0]);
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
	#[NoReturn] public function post(array $args){
		$post_key = ['name' => 0, 'description' => 0, 'start' => 0, 'duration' => 0, 'logo' => 0, 'RNA' => 0];
		$file_key = ['filename' => 0, 'type' => 0, 'content' => 0];
		$user_token = $this->tm->selectByToken($args['uri_args'][0]);
		$values = array_intersect_key($args['post_args'], $post_key);
		if(count($values) !== 6){
			response(400, "Bad Request");
		}
		$values['logo'] = array_intersect_key($values['logo'], $file_key);
		if(count($values['logo']) !== 3){
			response(400, "Bad Request");
		}
		if($values['logo']['type'] !== "image/png"){
			response(400, "Only Png image accepted");
		}
		var_dump($values['logo']['content']);
		if(!is_png(base64_decode($values['logo']['content']))){
			response(400, "Bad request");
		}
		$start = DateTime::createFromFormat("Y-m-d", $values['start']);
		if($start === false){
			response(400, "Invalid date format");
		}
		if($start->diff(new DateTime())->invert === 0){
			response(400, "Start date in past");
		}
		if(!$this->checkRNA($values['RNA'])){
			response(400, "Bad RNA");
		}
		$save_name = md5(time() . $values['logo']['filename']) . ".b64";
		if(file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/images/offer/" . $save_name, $values['logo']['content']) === false){
			response(500, "Internal Server Error");
		}
		$file_id = $this->fm->insert(['file_path' => "images/offer/" . $save_name, "file_name" => $values['logo']['filename'], 'type' => $values['logo']['type'], 'creator' => $user_token['user']]);
		if($file_id === false){
			response(501, "Internal Server Error");
		}
		$values['logo'] = $file_id;
		if($this->pm->insert($values)){
			response(200, "Project Added");
		}
		response(502, "Internal Server Error");
	}

	private function checkRNA(string $RNA): bool{
		$curl = curl_init();
		$opt = [
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_URL => "https://entreprise.data.gouv.fr/api/rna/v1/id/$RNA",
			CURLOPT_RETURNTRANSFER => true
		];
		curl_setopt_array($curl, $opt);
		$res = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		return $http_code === 200;
}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		if(!checkToken($args['uri_args'][0], 3)){
			response(401, "Unauthorized");
		}
		$project = $this->pm->select($args['uri_args'][1]);
		if($project === false){
			response(404, "Not Found");
		}
		if($this->pm->update($project['id'], ['valid' => 1])){
			response(200, "Project Validated");
		}
		response(500, "Internal Server Error");
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}