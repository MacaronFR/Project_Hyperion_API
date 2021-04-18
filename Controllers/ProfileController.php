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
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		//$am = new AddressModel();
		var_dump($this->am->insert(["zip" => 77830, "address" => "nik", "city" => "pamfou", "country" => "NIKMAND", "region" => "saisap"]));
	}
}