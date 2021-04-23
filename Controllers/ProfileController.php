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
			if(isset($args['put_args'])){
			if(isset($args['uri_args']) && is_numeric($args['uri_args'][1])){
				$user_info = $this->um->select($args['put_args']);
				if( $user_info !== false){
					$address_info = $this->am->select($user_info['addr']);
					if($tmp = array_intersect_key($args('put_args'),$user_info)){
						if($this->um->update($tmp['id'],$tmp)){
							response(200,"Profile Updated");
						}else{
							response(202,'No update');
						}
					}
					if($temp = array_merge($args['put_args'],$address_info)){
						if($this->am->selectIdentical($temp)){
							var_dump($temp);
						}
					}

					}else{
					response(404, "Not Found");
				}

			}
				}else{
					response(404,"Not Found");
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