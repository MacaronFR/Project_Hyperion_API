<?php


namespace Hyperion\API;

use Cassandra\Date;
use DateTime;

require_once "autoload.php";

class ConnectionController implements Controller{
	private UserModel $userM;
	private TokenModel $tm;
	private DateTime $now;
	private ClientModel $cm;

	public function __construct(){
		$this->userM = new UserModel();
		$this->tm = new TokenModel();
		$this->now = new DateTime();
		$this->cm = new ClientModel();
	}

	/**
	 * Connect user by providing client credentials and user credentials
	 * @inheritDoc
	 */
	public function get(array $args){
		$clientInfo = $this->cm->selectFromClientID($args['uri_args'][0]);
		if($clientInfo !== false && $args['uri_args'][1] === $clientInfo['secret']){
			$user = $this->userM->selectFromMail($args['uri_args'][2]);
			if($user['password'] === $args['uri_args'][3]){
				$message = "/token/" . $args['uri_args'][0] . "/" . $args['uri_args'][1] . "/" . $args['uri_args'][2] . "/" . $args['uri_args'][3];
				if(!$this->userM->update($user['id_user'], ['llog' => (new DateTime())->format("Y-m-d H:i:s")]))
					$message = "Error on updating last_login date";
				response(302, $message, ['id' => $user['id_user'], 'type' => $user['type'], 'name' => $user['name'], 'mail' => $args['uri_args'][2]]);
			}else{
				response(403, "Unauthorized access");
			}
		}
	}

	/**
	 * Inscription
	 * @inheritDoc
	 */
	public function post(array $args){

		$token = $this->tm->selectByToken($args['uri_args'][0]);
		if($token !== false && ((int)$token['scope']) === 0){
			$values = $args['post_args'];
			if(isset($values['name'], $values['fname'], $values['mail'], $values['passwd']) && count($values) === 4){
				if($this->userM->selectFromMail($values['mail']) !== false){
					response(400, "Mail already exist");
					return;
				}
				$values['gc'] = 0;
				$values['type'] = 0;

				$values['llog'] = $this->now->format("Y-m-d H:i:s");
				$values['ac_creation'] = $this->now->format("Y-m-d H:i:s");
				$values['addr'] = null;
				if($this->userM->insert($values)){
					response(201, "User Created");
				}else{
					response(500, "Error creating user");
				}
			}else{
				response(400, "Bad Request, mismatch in given argument");
			}
		}else{
			response(401, "Invalid Credentials");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		response("405", "Method PUT not allowed");
	}

	/**
	 * Disconnecting
	 * @inheritDoc
	 */
	public function delete(array $args){
		$token_id = $this->tm->selectByToken($args["uri_args"][0]);
		if($token_id !== false){
			if($this->tm->delete($token_id['id_token'])){
				response(200, "Disconnected");
			}else{
				response(500, "Error during disconnection");
			}
		}else{
			response(404, "User token not found");
		}
	}
}