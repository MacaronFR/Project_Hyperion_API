<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class InvoiceController implements Controller{
	private InvoiceModel $im;
	private UserModel $um;
	private TokenModel $tm;

	public function __construct(){
		$this->im = new InvoiceModel();
		$this->um = new UserModel();
		$this->tm = new TokenModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(checkToken($args['uri_args'][0],3)){
			$this->getAdmin();
		}else{
			if(checkToken($args['uri_args'][0],5)){
				$this->getUserInvoice($args);
			}
		}
	}
	private function getAdmin(){
		if($this->im->selectAll(limit:false)){
			response(200,"All Invoices of every users");
		}else{
			response(404,"Not Found");
		}
	}

	 private function getUserInvoice(array $args){
		$user = getUser($this->tm,$args['uri_args'][0],$this->um);
		if($user){
			$invoice = $this->im->select($user['id_user'], "id_user");
			if($invoice === true){
				response(200, "All of your Invoices are belong to us",$invoice);
			}else{
				response(404, "Not Found");
			}
		}else{
			response(404, 'Not Found');
		}

	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		// TODO: Implement post() method.
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}

}

