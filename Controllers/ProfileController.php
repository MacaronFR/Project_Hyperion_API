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
				$then = DateTime::createFromFormat("Y-m-d H:i:s", $token['end']);
				if($this->now->diff($then)->invert === 0){
					$user = $this->um->select($token['user']);
					$user["addr"] = $this->am->select($user['addr']);
					response(200, "User info", $user);
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
	#[NoReturn] public function post(array $args){
		return false;
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		if(!isset($args['additional']) && !checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!isset($args['uri_args']) || (!isset($args['additional']) && !is_numeric($args['uri_args'][1]))){
			response(400, "Bad Request");
		}
		if(!isset($args['put_args'])){
			response(400, "Bad Request");
		}
		$token_info = $this->tm->selectByToken($args['uri_args'][0]);
		if(!isset($args['additional'])){
			$user_info = $this->um->select($args['uri_args'][1]);
		}else{
			$user_info = $this->um->select($token_info['user']);
		}
		unset($args['put_args']['id']);
		if($user_info === false){
			response(404, "User not found");
		}
		if(!isset($args['additional']) && (int)$token_info['scope'] !== 0 && $token_info['user'] !== $args['uri_args'][1] && $token_info['scope'] >= $user_info['type']){
			response(403, "Forbidden");
		}
		if(isset($args['put_args']['type']) && (int)$args['put_args']['type'] > $token_info['scope']){
			response(403, "Forbidden");
		}
		var_dump($args);
		if(isset($args['additional'])){
			unset($args['put_args']['id'], $args['put_args']['gc'], $args['put_args']['type'], $args['put_args']['llog'], $args['put_args']['ac_creation'], $args['put_args']['mail']);
		}
		$address_keys = ["address", "zip", "city", "country", "region"];
		if(isset($args['put_args']['addr']) && is_array($args['put_args']['addr'])){
			if($user_info['addr'] !== null){
				$address_info = $this->am->select($user_info['addr']);
				unset($address_info['id']);
				$new_address = array_merge($address_info, array_intersect_key($args['put_args']['addr'], $address_info));
			}else{
				if(array_intersect($address_keys, array_keys($args['put_args']['addr'])) !== $address_keys){
					response(400, "Bad Request");
				}
				foreach($address_keys as $name){
					$new_address[$name] = $args['put_args']['addr'][$name];
				}
			}
			$exist = $this->am->selectIdentical($new_address);
			if($exist !== false){
				$args['put_args']['addr'] = $exist['id'];
			}else{
				$new_id = $this->am->insert($new_address);
				if($new_id === false){
					response(500, "Internal Server Error");
				}
				$args['put_args']['addr'] = $new_id;
			}
		}
		$user_update = array_intersect_key($args['put_args'], $user_info);
		var_dump($user_update, $user_info, $args['put_args']);
		if(!empty($user_update)){
			var_dump("ici");
			if($this->um->update($user_info['id'], $user_update)){
				response(200, "Profile Updated");
			}else{
				//response(204, "No update");
			}
		}else{
			response(400, "Bad Request");
		}
	}

	/**
	 * @inheritDoc
	 */
	 public function delete(array $args){
		if(checkToken($args['uri_args'][0],3)){
			$user =  $this->um->select($args['uri_args'][1]);
			if($user){
				$this->um->delete($args['uri_args'][1]);
				response(204,"User Deleted");
			}

		}else{
			if(checkToken($args['uri_args'][0],1))
			$user =  $this->um->select($args['uri_args'][0]);
			if($user){
				$token = $this->tm->update(-1,$args['uri_args'][0]);
				if($token){
					response(204,"USER Deleted");
				}
			}
		}

	}
}