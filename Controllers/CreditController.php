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
		if($args['additional'][0] === "one"){
			$this->getOne($args);
		}elseif($args['additional'][0] === "all"){
			$this->getAll($args);
		}
	}

	#[NoReturn] private function getAll(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Unauthorized");
		}
		if(isset($args['uri_args'][1])){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$invoice = $this->im->selectAllCredit($args['uri_args'][1]);
		}else{
			$invoice = $this->im->selectAllCredit(limit: false);
		}
		if($invoice === false){
			response(500, "Internal Server Error");
		}
		if(empty($invoice)){
			response(204, "No Content");
		}
		$total = $this->im->selectAllCreditTotal();
		if($total === false){
			response(501, "Internal Server Error");
		}
		$invoice['total'] = $invoice['totalNotFiltered'] = $total;
		response(200, "All Invoice", $invoice);
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
		$total = $this->im->selectAllByUserCreditTotal($user['id']);
		if($total === false){
			response(501, "Internal Server Error");
		}
		$invoice['total'] = $invoice['totalNotFiltered'] = $total;
		response(200, "Credits", $invoice);
	}

	#[NoReturn] private function getOne(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$invoice = $this->im->select($args['uri_args'][1]);
		if($invoice === false){
			response(404, "Invoice Not Found");
		}
		if($invoice['offer'] === null){
			response(400, "Bad Request");
		}
		if(!checkToken($args['uri_args'][0], 3)){
			$user = getUser($this->tm, $args['uri_args'][0], $this->um);
			if($user['id'] !== $invoice['user']){
				response(401, "Unauthorized");
			}
		}
		$file = $this->fm->selectWithB64($invoice['file']);
		if($file === false){
			response(500, "Internal Server Error");
		}
		unset($file['creator'], $file['file_path']);
		$invoice['file'] = $file;
		response(200, "Invoice", $invoice);
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