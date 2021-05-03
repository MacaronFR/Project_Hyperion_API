<?php


namespace Hyperion\API;

use DateTime;
use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";

class ConnectionController implements Controller{
	private UserModel $um;
	private TokenModel $tm;
	private DateTime $now;
	private ClientModel $cm;

	public function __construct(){
		$this->um = new UserModel();
		$this->tm = new TokenModel();
		$this->now = new DateTime();
		$this->cm = new ClientModel();
	}

	/**
	 * Connect user by providing client credentials and user credentials
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		$clientInfo = $this->cm->selectFromClientID($args['uri_args'][0]);
		if($clientInfo === false || $args['uri_args'][1] !== $clientInfo['secret']){
			response(403, "Forbidden");
		}
		$user = $this->um->selectFromMail($args['uri_args'][2]);
		if($user === false){
			response(500, "Internal Server Error");
		}
		if($user['password'] !== $args['uri_args'][3]){
			response(403, "Forbidden");
		}
		if(!$this->um->update($user['id_user'], ['llog' => (new DateTime())->format("Y-m-d H:i:s")]))
			response(500, "Internal Server Error");
		$message = "/token/" . $args['uri_args'][0] . "/" . $args['uri_args'][1] . "/" . $args['uri_args'][2] . "/" . $args['uri_args'][3];
		response(302, $message, ['id' => $user['id_user'], 'type' => $user['type'], 'name' => $user['name'], 'mail' => $args['uri_args'][2]]);
	}

	/**
	 * Inscription
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(!checkToken($args['uri_args'][0], 1)){
			response(401, "Invalid Credentials");
		}
		$values = $args['post_args'];
		if(!isset($values['name'], $values['fname'], $values['mail'], $values['passwd']) || count($values) !== 4){
			response(400, "Bad Request");
		}
		if($this->um->selectFromMail($values['mail']) !== false){
			response(409, "Mail already exist");
		}
		$values['gc'] = 0;
		$values['type'] = 4;
		$values['llog'] = $this->now->format("Y-m-d H:i:s");
		$values['ac_creation'] = $this->now->format("Y-m-d H:i:s");
		$values['addr'] = null;
		if($this->um->insert($values)){
			API_log($args['uri_args'][0], "USERS", "Creating user");
			response(201, "User Created");
		}
		response(500, "Error creating user");
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		response("405", "Method PUT not allowed");
	}

	/**
	 * Disconnecting
	 * @inheritDoc
	 */
	#[NoReturn] public function delete(array $args){
		$token_id = $this->tm->selectByToken($args["uri_args"][0]);
		if($token_id === false){
			response(404, "User token not found");
		}
		API_log($args['uri_args'][0], "USERS", "Disconnect User");
		if($this->tm->delete($token_id['id'])){
			response(200, "Disconnected");
		}
		response(500, "Error during disconnection");
	}
}