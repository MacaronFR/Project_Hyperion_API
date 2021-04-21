<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";


// get all types
class ProductHierarchyController implements Controller{
	private TypeModel $tm;
	private CategoryModel $cm;
	private ProductModel $pm;

	public function __construct(){
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
		$this->pm = new ProductModel();
	}

	/**
	 * execute Product Hierarchy on category and type
	 * @param array $args same as get() $args
	 */

	#[NoReturn] private function product(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
			$type = $this->tm->select($args['uri_args'][0]);
			if($type !== false){
				$products = $this->pm->selectAllByType((int)$args['uri_args'][0], $iteration);
				if($products !== false){
					if(count($products) !== 0){
						foreach($products as &$prod){
							$spec = $this->pm->selectWithDetail($prod['id']);
							if($spec !== false){
								$prod = array_merge($prod, $spec);
							}else{
								response(500, "Internal Error");
							}
						}
						response(200, "Product from type ${type['type']}", $products);
					}else{
						response(204, "No Content");
					}
				}else{
					response(500, 'Error retrieving products');
				}
			}else{
				response(404, "Type Not Found");
			}
		}else{
			response(400, "Bad Request");
		}
	}


	#[NoReturn] private function mark_prod(array $args){
		if(count($args['uri_args']) === 2){
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
		if(count($reference) === 0){
			response(204, "No Content");
		}
		foreach($reference as &$ref){
			$spec = $this->pm->selectWithDetail($ref['id']);
			if($spec === false){
				response(500, "Internal Server Error");
			}
			$ref = array_merge($ref, $spec);
		}
		response(200, "Reference from mark $mark", $reference);
	}

	private function type_mark_prod($args){
		if(count($args['uri_args']) === 3){
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$type = $this->tm->select($args['uri_args'][0]);
		if(is_numeric($args['uri_args'][0])){
			$mark_name = $args['uri_args'][1];
			$type_name = $type['type'];
			$reference = $this->pm->selectAllByTypeMark((int)$type['id'], $mark_name, $iteration);
			foreach($reference as &$ref){
				$spec = $this->pm->selectWithDetail($ref['id']);
				$ref = array_merge($ref, $spec);
			}
			response(200, "Product from mark $mark_name of type $type_name", $reference);
		}
	}

	#[NoReturn] private function model_prod($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][0])){
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

	public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type_product'){
				$this->product($args);
			}elseif($args['additional'][0] === 'mark_product'){
				$this->mark_prod($args);
			}elseif($args['additional'][0] === 'type_mark_product'){
				$this->type_mark_prod($args);
			}elseif($args['additional'][0] === 'model_product'){
				$this->model_prod($args);
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
