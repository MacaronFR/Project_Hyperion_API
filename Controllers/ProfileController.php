<?php


namespace Hyperion\API;


use DateTime;

class ProfileController implements Controller{

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
					$um = new UserModel();
					$adm = new AddressModel();
					$user = $um->select($token['id_client']);
					$address = $adm->select($user['address']);
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
		$am = new AddressModel();
		var_dump($am->insert(["zip" => 77830, "address" => "nik", "city" => "pamfou", "country" => "NIKMAND", "region" => "saisap"]));
	}
}