<?php


namespace Hyperion\API;


use DateTime;
use JetBrains\PhpStorm\Internal\PhpStormStubsElementAvailable;
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
		if(!isset($args['additional'])){
			$this->getProfile($args);
		}elseif($args['additional'][0] === "users"){
			$this->getUsers($args);
		}
	}

	#[NoReturn] private function getProfile(array $args){
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
			if(!checkToken($args['uri_args'][0], 3)){
				response(401, "Unauthorized");
			}
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad request");
			}
			$user = $this->um->select($args['uri_args'][1]);
			if($user === false){
				response(404, "User Not Found");
			}
			if($user['addr'] !== null){
				$user['addr'] = $this->am->select($user['addr']);
				if($user['addr'] === false){
					response(500, "Internal Server Error");
				}
			}
			response(200, "User", $user);
		}
	}

	#[NoReturn] private function getUsers(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(401, "Unauthorized");
		}
		if(isset($args['uri_args'][1])){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			if(count($args['uri_args']) > 2){
				$order = $args['uri_args'][3] ?? 'ASC';
				$order = strtoupper($order);
				if($order !== "ASC" && $order !== "DESC"){
					response(400, "Bad Request");
				}
				$search = $args['uri_args'][2];
				$sort = $args['uri_args'][4] ?? 'id';
				if($sort !== "id" && $sort !== "name" && $sort !== "fname" && $sort !== "mail" && $sort !== "type"){
					response(400, "Baq Request");
				}
				$users = $this->um->selectAllFilter($search, $order, $sort, $args['uri_args'][1]);
				$totalFilter = $this->um->selectTotalFilter($search, $order, $sort);
				$total = $this->um->selectTotal();
			}else{
				$total = $totalFilter = $this->um->selectTotal();
				$users = $this->um->selectAll((int)$args['uri_args'][1]);
			}
		}else{
			$total = $totalFilter = $this->um->selectTotal();
			if($total === false){
				response(502, "Internal Server Error");
			}
			$users = $this->um->selectAll(limit: false);
		}
		if($users === false){
			response(500, "Internal Server Error");
		}
		foreach($users as &$u){
			if($u['addr'] !== null){
				$u['addr'] = $this->am->select($u['addr']);
				if($u['addr'] === false){
					response(501, "Internal Server Error");
				}
			}
		}
		if(empty($users)){
			response(204, "No Users");
		}
		$users['total'] = $totalFilter;
		$users['totalNotFiltered'] = $total;
		response(200, "Users", $users);
	}

	/**
	 * @inheritDoc
	 */
	#[
		NoReturn] public function post(array $args){
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
		if(isset($args['put_args']['type']) && (int)$args['put_args']['type'] < $token_info['scope']){
			response(403, "Forbidden");
		}
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
		if(!empty($user_update)){
			if($this->um->update($user_info['id'], $user_update)){
				response(200, "Profile Updated");
			}else{
				response(204, "No update");
			}
		}else{
			response(400, "Bad Request");
		}
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function delete(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$token = $this->tm->selectByToken($args['uri_args'][0]);
		if($token === false){
			response(403, "Forbidden");
		}
		$user = $this->um->select($args['uri_args'][1]);
		if($user === false){
			response(404, "User Not Found");
		}
		if((int)$user['type'] <= (int)$token['scope']){
			response(401, "Unauthorized");
		}
		if($this->um->delete($user['id'])){
			response(204, "User deleted");
		}
		response(500, "Internal Server Error");
	}
}