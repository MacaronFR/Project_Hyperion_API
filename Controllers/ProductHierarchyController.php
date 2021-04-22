<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";


// get all types
class ProductHierarchyController implements Controller{
	private ProductModel $pm;

	public function __construct(){
		$this->pm = new ProductModel();
	}

	#[NoReturn] private function type_product(array $args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$products = $this->pm->selectAllByType((int)$args['uri_args'][0], $iteration);
		if($products === false){
			response(500, "Internal Server Error");
		}
		if(empty($products)){
			response(204, "No Content");
		}
		response(200, "Product from type " . $products[0]['type'], $products);
	}


	#[NoReturn] private function mark_prod(array $args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(!isset($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$mark = $args['uri_args'][0];
		$reference = $this->pm->selectAllByMark($mark, $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No Content");
		}
		response(200, "Product of mark $mark", $reference);
	}

	#[NoReturn] private function type_mark_prod($args){
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad request");
		}
		$prod = $this->pm->selectAllByTypeMark((int)$args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($prod === false){
			response(500, "Internal Server Error");
		}
		if(empty($prod)){
			response(204, "No content");
		}
		response(200, "Product of type " . $prod[0]['type'] . ", mark " . $prod[0]['mark'], $prod);
	}

	#[NoReturn] private function model_prod($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$product = $this->pm->selectAllByModel($args['uri_args'][0], $iteration);
		if($product === false){
			response(500, "Internal Server Error");
		}
		if(empty($product)){
			response(204, "No content");
		}
		$iteration++;
		response(200, "Product of model " . $args['uri_args'][0] . " page $iteration", $product);
	}

	#[NoReturn] private function type_model_prod($args){
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$product = $this->pm->selectAllByTypeModel($args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($product === false){
			response(500, "Internal Server Error");
		}
		if(empty($product)){
			response(204, "No content");
		}
		$iteration++;
		response(200, "Product of type " . $product[0]['type'] . ", model " . $product[0]['model'] . ", page $iteration", $product);
	}

	#[NoReturn] public function mark_model_prod(array $args){
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$products = $this->pm->selectAllByMarkModel($args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($products === false){
			response(500, "Internal Server Error");
		}
		if(empty($products)){
			response(204, "No content");
		}
		response(200, "Product of mark , model ", $products);
	}

	#[NoReturn] public function type_mark_model_prod(array $args){
		if(count($args['uri_args']) === 4){
			if(!is_numeric($args['uri_args'][3])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][3];
		}else{
			$iteration = 0;
		}
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$product = $this->pm->selectAllByTypeMarkModel($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2], $iteration);
		if($product === false){
			response(500, "Internal Server Error");
		}
		if(empty($product)){
			response(204, "No Content");
		}
		response(200, "Product of type " . $product[0]['type'] . ", mark " . $product[0]["mark"] . ", model " . $product[0]['model'], $product);
	}

	public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type_product'){
				$this->type_product($args);
			}elseif($args['additional'][0] === 'mark_product'){
				$this->mark_prod($args);
			}elseif($args['additional'][0] === 'type_mark_product'){
				$this->type_mark_prod($args);
			}elseif($args['additional'][0] === 'model_product'){
				$this->model_prod($args);
			}elseif($args['additional'][0] === 'mark_model_product'){
				$this->mark_model_prod($args);
			}elseif($args['additional'][0] === 'type_model_product'){
				$this->type_model_prod($args);
			}elseif($args['additional'][0] === 'type_mark_model_product'){
				$this->type_mark_model_prod($args);
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		return false;
	}

}
