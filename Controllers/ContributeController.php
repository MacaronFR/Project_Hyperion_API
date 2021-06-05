<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ContributeController implements Controller{
	private ContributeModel $cm;
	private UserModel $um;
	private TokenModel $tm;
	private ProjectModel $pjm;

	public function __construct(){
		$this->cm = new ContributeModel();
		$this->um = new UserModel();
		$this->tm = new TokenModel();
		$this->pjm = new ProjectModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		// TODO: Implement get() method.
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(!checkToken($args['uri_args'][0], 5)){
			response(403, "Forbidden");
		}

		if(!is_numeric($args["post_args"]["amount"]) || !is_numeric($args['post_args']['project'])){
			response(400, "Bad Request");
		}

		$token = $this->tm->selectByToken($args['uri_args'][0]);
		$user = $this->um->select($token['user']);
		if($user === false){
			response(500, "Internal Server Error");
		}

		$project = $this->pjm->select($args['post_args']["project"]);
		if($project === false){
			response(404, "Not Found");
		}

		if($args["post_args"]["amount"] > $user["gc"]){
			response(403, "Not enough Green Coins");
		}

		$value = ["value" => $args["post_args"]["amount"],
			"project" => $args["post_args"]["project"],
			"user" => $user["id"]];
		if($this->cm->insert($value)){
			$new_gc = ["gc" => $user['gc'] - $args['post_args']['amount']];
			$this->um->update($user['id'], $new_gc);
			response(200, "OK");
		}

		response(500, "Internal Server Error");
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
