<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";

class StoreController implements Controller{
		private ProductModel $pm;

		public function __construct(){
				$this->pm = new ProductModel();
		}


		/**
		 * Retrieve the first 500 products on /store or the Nth 500 products for /store/N
		 * @inheritDoc
		 */
		#[NoReturn] public function get(array $args){
				if(count($args['uri_args']) === 0){
						$iteration = 0;
				}elseif(count($args['uri_args']) === 1){
						if(is_numeric($args['uri_args'][0])){
								$iteration = (int)$args['uri_args'][0];
						}else{
								response(400, "Bad Request");
						}
				}else{
						response(400, "Bad Request");
				}
				$products = $this->pm->selectAllDetails($iteration);
				if($products){
						$start = $iteration * 500 + 1;
						$end = ($iteration + 1) * 500;
						response(200, "Product $start to $end", $products);
				}else
						response(204, "No product found");
		}

		/**
		 * @inheritDoc
		 */
		#[NoReturn] public function post(array $args){
				return false;
		}

		/**
		 * @inheritDoc
		 */
		#[NoReturn] public function put(array $args){
				return false;
		}

		/**
		 * @inheritDoc
		 */
		#[NoReturn] public function delete(array $args){
				return false;
		}
}