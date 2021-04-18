<?php


namespace Hyperion\API;


use DateTime;

class ProfileController implements Controller{
	private UserModel $um;
	private AddressModel $am;
		public function __construct(){
				$this->um = new UserModel();
				$this->am = new AddressModel();
		}


	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		$token_model = new TokenModel();
		if(count($args["uri_args"]) === 1){
			$token = $token_model->selectByToken($args["uri_args"][0]);
			if($token !== false){
				$now = new DateTime();
				$then = DateTime::createFromFormat("Y-m-d H:i:s", $token['expire']);
				if($now->diff($then)->invert === 0){

					$user = $this->um->select($token['id_client']);
					$address = $this->am->select($user['address']);
					$info = [
						"name" => $user['name'],
						"fname" => $user['firstname'],
						"gc" => $user['green_coins'],
						"type" => $user['type'],
						"mail" => $user['mail'],
						"llog" => $user['last_login'],
						"ac_creation" => $user['account_creation'],
						"adress" => $address,
					];
					response(200, "User info", $info);
				}else{
					response(401, "Invalid Credentials");
				}
			}else{
				response(401, "Invalid Credentials");
			}
		}else{
			response(400, "Bad request");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){

	}

	/**
	 * @inheritDoc
	 */
			public function put(array $args){
					if(checkToken($args['uri_args'][0], 3)){
							if(count($args['put_args']) === 1 && isset($args['put_args']['name'])){
									$post = $this->um->select($args['uri_args'][1]);
									$postt = $this->am->select($args['uri_args'][1]);
									if($post !== false){
											if($post['name'] !== $args['put_args']['name']){
													if($this->um->update($args['uri_args'][1], $args['put_args'])){
															response(201, "Profile Updated");
													}else{
															response(500, "Error while updating");
													}
											}else{
													response(204, "No Content");
											}
									}else{
											response(400, "Bad Request");
									}
							}else{
									response(400, "Bad Request");
							}
							if($postt !== false){
									if($postt['name'] !== $args['put_args']['name']){
											if($this->am->update($args['uri_args'][1], $args['put_args'])){
													response(201, "Adress Updated");
											}else{
													response(500, "Errors while updating");
											}
									}else{
											reponse(204,"No Content");
									}
							}else{
									response(400,"Bad Request");
							}
					}else{
							response(403, "Forbidden");
					}

			}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		//$am = new AddressModel();
		var_dump($this->am->insert(["zip" => 77830, "address" => "nik", "city" => "pamfou", "country" => "NIKMAND", "region" => "saisap"]));
	}
}