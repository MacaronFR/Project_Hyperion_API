<?php


namespace Hyperion\API;
use DateTime;
use DateInterval;
require_once "autoload.php";

class OAuthController extends Controller
{
	private function newToken(int $scope, int $client, int $user){
		$tokenM = new TokenModel();
		do {
			$token = bin2hex(random_bytes(32));
		}while($tokenM->selectByToken($token) !== false);
		$end = new DateTime();
		$end->add(new DateInterval("PT2H"));
		$value = [
			'token' => $token,
			'scope' => $scope,
			'end' => $end->format("Y-m-d H:i:s"),
			'client' => $client,
			'user' => $user
		];
		if($tokenM->insert($value)){
			response(200, "New token generated", ['token' => $token, 'expire' => $end->format("Y-m-d H:i:s")]);
		}else{
			response(500, "Error generating new token, please verify given information and retry");
		}
	}
	/**
	 * Send token to the client (new if no one exist or is expire and refresh in other case)
	 * @param array $args
	 */
	public function get(array $args){
		$clientM = new ClientModel();
		$tokenM = new TokenModel();
		if(count($args['uri_args']) === 4){
			$userM = new UserModel();
			$clientInfo = $clientM->selectFromClientID($args['uri_args'][0]);
			if($clientInfo !== false && $clientInfo['client_secret'] === $args['uri_args'][1]){
				$userInfo = $userM->selectFromMail($args['uri_args'][2]);
				if($userInfo !== false && $userInfo['password'] === hash('sha256', $args['uri_args'][3])){
					$token = $tokenM->selectByUser((int)$userInfo['id_user']);
					if($token !== false){
						$now = new DateTime();
						$diff = $now->diff(DateTime::createFromFormat("Y-m-d H:i:s", $token["expire"]));
						if($diff->invert === 1){
							$tokenM->delete($token['id_token']);
							$this->newToken($userInfo['type'], $clientInfo['id_client'], $userInfo['id_user']);
						}else{
							$new_end = $tokenM->refreshToken($token['id_token']);
							if($new_end === false){
								sleep(1);
								$new_end = $tokenM->refreshToken($token['id_token']);
							}
							response(200, "Token refreshed", [$token['value'], $new_end]);
						}
					}else{
						$this->newToken($userInfo['type'], $clientInfo['id_client'], $userInfo['id_user']);
					}
				}else{
					response(401, "Invalid users information");
				}
			}else{
				response(401, "Invalid credentials");
			}
		}else{
			response(204, "Under Build");
		}
	}
	/**
	 * @inheritDoc
	 */
	public function post(array $args){}
	/**
	 * @inheritDoc
	 */
	public function put(array $args){}
	/**
	 * @inheritDoc
	 */
	public function delete(array $args){}
}