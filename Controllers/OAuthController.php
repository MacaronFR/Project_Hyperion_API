<?php


namespace Hyperion\API;
use DateTime;
use DateInterval;
use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";

class OAuthController implements Controller
{
	private TokenModel $tm;
	private ClientModel $cm;
	private UserModel $um;
	private DateTime $end;
	private DateTime $now;

	public function __construct(){
		$this->tm = new TokenModel();
		$this->cm = new ClientModel();
		$this->um = new UserModel();
		$this->end = new DateTime();
		$this->now = new DateTime();
	}

	#[NoReturn] private function newToken(int $scope, int $client, int|null $user){
		do {
			$token = bin2hex(random_bytes(32));
		}while($this->tm->selectByToken($token) !== false);
		$this->end->add(new DateInterval("PT2H"));
		$value = [
			'token' => $token,
			'scope' => $scope,
			'end' => $this->end->format("Y-m-d H:i:s"),
			'client' => $client,
			'user' => $user
		];
		if($this->tm->insert($value)){
			response(200, "New token generated", ['token' => $token, 'expire' => $this->end->format("Y-m-d H:i:s")]);
		}else{
			response(500, "Error generating new token, please verify given information and retry");
		}
	}
	/**
	 * Send token to the client (new if no one exist or is expire and refresh in other case)
	 * @param array $args
	 */
	#[NoReturn] public function get(array $args){
		if(count($args['uri_args']) === 4){
			$clientInfo = $this->cm->selectFromClientID($args['uri_args'][0]);
			if($clientInfo !== false && $clientInfo['secret'] === $args['uri_args'][1]){
				$userInfo = $this->um->selectFromMail($args['uri_args'][2]);
				if($userInfo !== false && $userInfo['password'] === $args['uri_args'][3]){
					$token = $this->tm->selectByUser((int)$userInfo['id_user']);
					if($token !== false){
						$diff = $this->now->diff(DateTime::createFromFormat("Y-m-d H:i:s", $token["expire"]));
						if($diff->invert === 1){
							$this->tm->delete($token['id_token']);
							$this->newToken($userInfo['type'], $clientInfo['id_client'], $userInfo['id_user']);
						}else{
							do{
								$end =$this->now->add(new DateInterval("PT2H"));
								$refreshed = $this->tm->update($token['id_token'], ["end" => $end->format("Y-m-d H:i:s")]);
							}while(!$refreshed);
							response(200, "Token refreshed", ['token' => $token['value'], 'expire' => $end->format("Y-m-d H:i:s")]);
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
		}elseif(count($args['uri_args']) === 2){
			$clientInfo = $this->cm->selectFromClientID($args['uri_args'][0]);
			if($clientInfo !== false && $clientInfo['secret'] === $args['uri_args'][1]){
				$token = $this->tm->selectByClient($clientInfo['id_client']);
				if($token !== false){
					$now = new DateTime();
					$diff = $now->diff(DateTime::createFromFormat("Y-m-d H:i:s", $token["expire"]));
					if($diff->invert === 1){
						$this->tm->delete($token['id_token']);
						$this->newToken($clientInfo['scope'], $clientInfo['id_client'], null);
					}else{
						do{
							$end = $now->add(new DateInterval("PT2H"));
							$refreshed = $this->tm->update($token['id_token'], ["end" => $end->format("Y-m-d H:i:s")]);
						}while(!$refreshed);
						response(200, "Token refreshed", ['token' => $token['value'], 'expire' => $end->format("Y-m-d H:i:s")]);
					}
				}else{
					$this->newToken($clientInfo['scope'], $clientInfo['id_client'], null);
				}
			}else{
				response(401, "Invalid Credentials");
			}
		}
	}
	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){}
	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){}
	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function delete(array $args){}
}