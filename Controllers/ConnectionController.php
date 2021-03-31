<?php


namespace Hyperion\API;

use \DateTime;
use JetBrains\PhpStorm\Pure;

require_once "autoload.php";

class ConnectionController extends Controller
{
	private UserModel $userM;
	#[Pure]
	public function __construct(){
		 $this->userM = new UserModel();
	}
	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		$clientM = new ClientModel();
		$clientInfo = $clientM->selectFromClientID($args['uri_args'][0]);
		if($clientInfo !== false && $args['uri_args'][1] === $clientInfo['client_secret']) {
			$user = $this->userM->selectFromMail($args['uri_args'][2]);
			if ($user['password'] === $args['uri_args'][3]) {
				$message = "Redirect to get new token";
				if (!$this->userM->update($user['id_user'], ['llog' => (new DateTime())->format("Y-m-d H:i:s")]))
					$message = "Error on updating last_login date";
				header("Location: /token/" . $args['uri_args'][0] . "/" . $args['uri_args'][1] . "/" . $args['uri_args'][2] . "/" . $args['uri_args'][3]);
				response(302, $message, ['id' => $user['id_user'], 'type' => $user['type'], 'name' => $user['name'], 'mail' => $args['uri_args'][2]]);
			} else {
				response(403, "Unauthorized access");
			}
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