<?php


namespace Hyperion\API;


use DateTime;
use JetBrains\PhpStorm\NoReturn;
class ProfileController implements Controller{
	private UserModel $um;
	private AddressModel $am;
	private DateTime $now;
	private TokenModel $tm;

	public function __construct(){
		$this->um = new UserModel();
		$this->am = new AddressModel();
		$this->now = new DateTime();
		$this->tm = new TokenModel();
	}


	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if(count($args["uri_args"]) === 1){
			$token = $this->tm->selectByToken($args["uri_args"][0]);
			if($token !== false){
				$then = DateTime::createFromFormat("Y-m-d H:i:s", $token['expire']);
				if($this->now->diff($then)->invert === 0){

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
						"address" => $address,
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
			return false;

	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
			if(checkToken($args['uri_args'][0], 3)){
					if(count($args['put_args']) === 1 && isset($args['put_args'])){
							$user_info = $this->um->select($args['put_args']);
							if($user_info !== false){
									if(array_intersect_key($user_info['name'], $args['put_args'])){
											$user_info->update($args['put_args']);
									}
									if(array_intersect_key($user_info['firstname'], $args['put_args'])){
											$user_info->update($args['put_args']);
									}
									if(array_intersect_key($user_info['mail'], $args['put_args'])){
											$user_info->update($args['put_args']);
									}
									$address_info = $this->am->selectByUser($user_info['address']);
									if(array_intersect_key($user_info['address'], $address_info, $args['put_args'])){
											$update_address = $address_info->update($args['put_args']);
											$user_info->update($update_address);
											}
							}else{
									response(404, "Not Found");
							}
					}
			}
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
			var_dump($this->am->insert(["zip" => 77830, "address" => "nik", "city" => "pamfou", "country" => "NIKMAND", "region" => "saisap"]));
	}
}