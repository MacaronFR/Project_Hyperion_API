<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ShopProductController implements Controller{

	private ProductModel $pm;

	public function __construct(){
		$this->pm = new ProductModel();
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$product = $this->pm->select($args['uri_args'][0]);
		if($product === false){
			response(404, "Product Not Found");
		}
		if((int)$product['status'] !== 2){
			response(404, "Not found");
		}
		$spec = $this->pm->selectWithDetail($product['id']);
		if($spec === false){
			response(500, "Internal Server Error");
		}
		$product['spec'] = $spec['spec'];
		response(200, "Product", $product);
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