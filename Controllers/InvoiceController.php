<?php


namespace Hyperion\API;


use DateTime;
use JetBrains\PhpStorm\NoReturn;

class InvoiceController implements Controller{
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
		if($args['additional'][0] === "admin"){
			$this->getAdmin();
		}elseif($args['additional'][0] === "me"){
			$this->getUserInvoice($args);
		}elseif($args['additional'][0] === "one"){
			$this->getOne($args);
		}elseif($args['additional'][0] === "cart"){
			$this->getFromCart($args);
		}
	}

	#[NoReturn] private function getFromCart(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$invoice = $this->im->select($args['uri_args'][1], 'cart');
		if($invoice === false){
			response(404, "Invoice Not Found");
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

	#[NoReturn] private function getOne(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$invoice = $this->im->select($args['uri_args'][1]);
		if($invoice === false){
			response(404, "Invoice Not Found");
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

	#[NoReturn] private function getAdmin(){
		if($this->im->selectAll(limit: false)){
			response(200, "All Invoices of every users");
		}else{
			response(404, "Not Found");
		}
	}

	#[NoReturn] private function getUserInvoice(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$invoice = $this->im->select($user['id_user'], "user");
		if($invoice === false){
			response(404, "Not Found");
		}
		response(200, " All of your Invoices are belong to us ", $invoice); // c'est un easter egg

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
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$cart = $this->cm->select($args['uri_args'][0]);
		if($cart === false){
			response(404, "Cart Not Found");
		}
		$products = $this->picm->selectByCart($cart['id']);
		if($products === false){
			response(500, "Internal Server Error");
		}
		foreach($products as $product){
			$p = $this->pm->select($product['product']);
			if($p === false){
				response(501, "Internal Server Error");
			}
			if((int)$p['status'] !== 2){
				response(404, "Product Not Found");
			}
			if(!$this->pm->update($product['product'], ['status' => 3, 'sell_d' => (new DateTime())->format("Y-m-d H:i:s")])){
				response(502, "Internal Server Error");
			}
		}
		$invoice = $this->im->select($cart['id'], "cart");
		if($invoice === false){
			response(503, "Internal Server Error");
		}
		if($this->im->update($invoice['id'], ['status' => 1])){
			response(200, "Invoice updated");
		}
		response(504, "Internal Server Error");
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}

}

