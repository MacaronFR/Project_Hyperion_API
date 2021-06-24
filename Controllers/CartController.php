<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class CartController implements Controller{

	private CartModel $cm;
	private ProductModel $pm;
	private ProductInCartModel $picm;
	private TokenModel $tm;
	private UserModel $um;
	private InvoiceModel $im;
	private FilesModel $fm;

	public function __construct(){
		$this->cm = new CartModel();
		$this->pm = new ProductModel();
		$this->picm = new ProductInCartModel();
		$this->tm = new TokenModel();
		$this->um = new UserModel();
		$this->im = new InvoiceModel();
		$this->fm = new FilesModel();
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if(!isset($args['additional'])){
			$this->getCart($args);
		}elseif($args['additional'][0] === "product"){
			$this->getCartProduct($args);
		}elseif($args['additional'][0] === "active"){
			$this->getCartActive($args);
		}
	}

	#[NoReturn] public function getCartActive(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$cart = $this->cm->selectByUser($user['id'], true);
		if($cart === false){
			response(404, "Not Found");
		}
		response(200, "Cart", $cart);
	}

	#[NoReturn] public function getCart(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$cart = $this->cm->select($args['uri_args'][1]);
		if($cart === false){
			response(404, "Not Found");
		}
		if($user['id'] !== $cart['user']){
			response(401, "Unauthorized");
		}
		response(200, "Cart", $cart);
	}

	#[NoReturn] public function getCartProduct(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$cart = $this->cm->selectByUser($user['id'], true);
		if($cart === false){
			response(404, "No Cart");
		}
		$products_in_cart = $this->picm->selectByCart($cart['id']);
		if($products_in_cart === false){
			response(500, "Internal Server Error");
		}
		if(empty($products_in_cart)){
			response(204, "No Product in cart");
		}
		$products = [];
		foreach($products_in_cart as $pid){
			$prod = $this->pm->select($pid['product']);
			if($prod === false){
				response(501, 'Internal Server Error');
			}
			unset($prod['buy_d'], $prod['buy_p'], $prod['sell_d'], $prod['offer'], $prod['ref']);
			$spec = $this->pm->selectWithDetail($prod['id']);
			if($spec === false){
				response(502, "Internal Server Error");
			}
			$products[] = array_merge($prod, $spec['spec']);
		}
		response(200, "Product in cart", $products);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(!isset($args['additional'])){
			$this->newCart($args);
		}elseif($args['additional'][0] === "add_prod"){
			$this->addProd($args);
		}
	}

	#[NoReturn] public function newCart(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$active_cart = $this->cm->selectByUser($user['id'], true);
		if($active_cart !== false){
			response(400, "Bad Request, Active Cart already exist", ['cart' => $active_cart['id']]);
		}
		$cart = $this->createCart($user['id']);
		if($cart === false){
			response(500, "Internal Server Error");
		}
		response(201, "New cart Created", ['id' => $cart]);
	}

	#[NoReturn] public function addProd(array $args){
		if(!isset($args['post_args']['product'])){
			response(400, "Bad Request");
		}
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$active_cart = $this->cm->selectByUser($user['id'], true);
		if($active_cart === false){
			$active_cart = $this->createCart($user['id']);
			if($active_cart === false){
				response(500, "Internal Server Error");
			}
			$active_cart = $this->cm->select($active_cart);
		}
		$product = $this->pm->select($args['post_args']['product']);
		if($product === false){
			response(404, "Product Not Found");
		}
		if((int)$product['status'] !== 2){
			response(404, "Invalid Product");
		}
		if($this->picm->selectIdentical($active_cart['id'], $product['id']) !== false){
			response(400, "Product already in cart");
		}
		if(!$this->picm->insert(['product' => $product['id'], 'cart' => $active_cart['id']])){
			response(501, "Internal Server Error");
		}
		if(($product['sell_p'] ?? 0) !== 0){
			if(!$this->cm->update($active_cart['id'], ['total' => ((double)$product['sell_p'] + (double)$active_cart['total'])])){
				response(502, "Internal Server Error");
			}
		}
		response(200, "Product Add to Cart");
	}

	private function createCart($user): int|false{
		$cart = $this->cm->insert(['user' => $user]);
		if($cart === false){
			return false;
		}
		return $cart;
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		$user = getUser($this->tm, $args['uri_args'][0],$this->um);
		$cart = $this->cm->selectByUser($user['id'], true);
		if($cart === false){
			response(404, "No active cart");
		}
		if($cart['user'] !== $user['id']){
			response(401, "Unauthorized");
		}
		if(!$this->cm->update($cart['id'], ['status' => 1])){
			response(500, "Internal Server Error");
		}
		$file_content = $this->getInvoice($cart['id'], $user);
		$save_name = md5(time() . "facture.pdf") . "b64";
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/images/invoice/" . $save_name, $file_content);
		$file_id = $this->fm->insert(['filename' => "facture.pdf", "file_path" => "images/invoice/" . $save_name, "type" => "application/pdf", "creator" => $user['id']]);
		$invoice_value = [
			'total' => $cart['total'],
			'file' => $file_id,
			'cart' => $cart['id'],
			'user' => $user['id']
		];
		$invoice_id = $this->im->insert($invoice_value);
		if($invoice_id === false){
			response(500, "Internal Server Error");
		}
		response(200, "Command OK, Invoice generated", ['invoice' => $invoice_id]);
	}

	private function getInvoice($id_cart, $user): string{
		include "Invoice.php";
		header("Content-type: application/pdf");
		return base64_encode($pdf_output);
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		if(!isset($args['additional'])){
			$this->deleteCart($args);
		}elseif($args['additional'][0] === "prod"){
			$this->deleteCartProd($args);
		}
	}

	#[NoReturn] private function deleteCart(array $args){
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$cart = $this->cm->selectByUser($user['id'], true);
		if($cart === false){
			response(404, "No active cart");
		}
		if(!$this->cm->update($cart['id'], ['status' => -1])){
			response(500, "Internal Server Error");
		}
		response(204, "Cart Deleted");
	}

	#[NoReturn] private function deleteCartProd(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$user = getUser($this->tm, $args['uri_args'][0], $this->um);
		$cart = $this->cm->selectByUser($user['id'], true);
		if($cart === false){
			response(404, "No active cart");
		}
		$prod = $this->pm->select($args['uri_args'][1]);
		if($prod === false){
			response(404, "Not Found");
		}
		$is_in_cart = $this->picm->selectIdentical($cart['id'], $prod['id']);
		if($is_in_cart === false){
			response(400, "Bad Request");
		}
		if($this->picm->delete($is_in_cart['id']) === false){
			response(500, "Internal Server Error");
		}
		if(((double)$prod['sell_p'] ?? 0) !== (double)0){
			if(!$this->cm->update($cart['id'], ['total' => ((double)$cart['total'] - (double)$prod['sell_p'])])){
				response(500, "Internal Server Error");
			}
		}
		response(204, "Cart Product Deleted");
	}
}