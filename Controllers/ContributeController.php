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
	public function post(array $args){
		if(checkToken($args['uri_args'][0],1)){
			if(is_numeric($args["post_args"]["amount"]) && is_numeric($args['post_args']['project'])){
				$token = $this->tm->selectByToken($args['uri_args'][0]);
				$user = $this->um->select($token['user']);
				if($user){
					$project = $this->pjm->select($args['post_args']["project"]);
					if($project){
						if($args["post_args"]["amount"] > $user["gc"]){
							response(403,"Not enought Green Coins");
						}else{
							$value = ["value"=>$args["post_args"]["amount"],
								"project"=>$args["post_args"]["project"],
								"user"=>$user["id"]];
							if($this->cm->insert($value)){
								$new_gc = ["gc"=>$user['gc']-$args['post_args']['amount']];
								$this->um->update($user['id'],$new_gc);
								response(200,"OK");
							}else{
								response(500,"Internal Server Error");
							}
						}
					}else{
						response(404,"Not Found");
					}
				}else{
					response(404,"Not Found");
				}
			}else{
				response(403,"Forbidden");
			}
		}else{
			response(403,"Forbidden");
		}
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
