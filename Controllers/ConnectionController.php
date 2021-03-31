<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

require_once "autoload.php";

class ConnectionController extends Controller
{
	private UserModel $users;
	public function __construct(){
		$this->users = new UserModel();
	}
	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		$user = $this->users->selectFromMail($args['uri_args'][0]);
		if($user['password'] === hash('sha256', $args['uri_args'][1])){
			$this->users->update($user['id_user'], []);
			response(200, "OK", ['id' => $user['id_user'], 'type' => $user['type'], 'name' => $user['name'], 'mail' => $args['uri_args'][0]]);
		}else{
			response(403, "Unauthorized access");
		}
	}

	/**
	 * @inheritDoc
	 *
	 */
	public function post(array $args){return false;}

	/**
	 * @inheritDoc
	 */
	public function put(array $args)
	{

	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){

	}
}