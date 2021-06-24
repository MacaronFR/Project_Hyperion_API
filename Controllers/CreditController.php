<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class CreditController implements Controller{

	private InvoiceModel $im;
	private UserModel $um;
	private TokenModel $tm;
	private FilesModel $fm;
	private CartModel $cm;
	private ProductInCartModel $picm;
	private ProductModel $pm;

	public function __construct(){
		$this->im = new InvoiceModel();
		$this->um = new UserModel();
		$this->tm = new TokenModel();
		$this->fm = new FilesModel();
		$this->cm = new CartModel();
		$this->picm = new ProductInCartModel();
		$this->pm = new ProductModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if($args['additional'][0] === "me"){
			$this->getUserCredit($args);
		}
	}

	#[NoReturn] private function getUserCredit(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		if(isset($args['uri_args'][1])){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$invoice = $this->im->selectAllByUserCredit($user['id'], $args['uri_args'][1]);
		}else{
			$invoice = $this->im->selectAllByUserCredit($user['id'], limit: false);
		}
		if($invoice === false){
			response(500, "Internal Server Error");
		}
		if(empty($invoice)){
			response(204, "No content");
		}
		response(200, "Credits", $invoice);
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

	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}