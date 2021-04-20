<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";


// get all types
class ProductHierarchyController implements Controller{
	private TypeModel $tm;
	private CategoryModel $cm;
	private ProductModel $pm;
	private ReferenceModel $rm;

	public function __construct(){
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
		$this->pm = new ProductModel();
		$this->rm = new ReferenceModel();
	}

	/**
	 * execute Product Hierarchy on category and type
	 * @param array $args same as get() $args
	 */
	#[NoReturn] private function type(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
			$cat = $this->cm->select($args['uri_args'][0]);
			if($cat !== false){
				$types = $this->tm->selectByCategory((int)$args['uri_args'][0], $iteration);
				if($types !== false){
					if(count($types) !== 0){
						response(200, "Type from category ${cat['name']}", $types);
					}else{
						response(204, "No Content");
					}
				}else{
					response(500, 'Error retrieving types');
				}
			}else{
				response(404, "Category Not Found");
			}
		}else{
			response(400, "Bad Request");
		}
	}

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

	#[NoReturn] private function reference(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
			$type = $this->tm->select($args['uri_args'][0]);
			if($type !== false){
				$reference = $this->rm->selectAllByType((int)$args['uri_args'][0], $iteration);
				if($reference !== false){
					if(count($reference) !== 0){
						foreach($reference as &$prod){
							$spec = $this->rm->selectWithDetail($prod['id']);
							if($spec !== false){
								$prod = array_merge($prod, $spec);
							}else{
								response(500, "Internal Error");
							}
						}
						response(200, "Reference from type ${type['type']}", $reference);
					}else{
						response(204, "No Content");
					}
				}else{
					response(500, 'Error retrieving reference');
				}
			}else{
				response(404, "Type Not Found");
			}
		}else{
			response(400, "Bad Request");
		}
	}

	#[NoReturn] private function mark_ref(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0])){
			$mark = $args['uri_args'][0];
			$reference = $this->rm->selectAllByMark($mark, $iteration);
			if($reference !== false){
				if(count($reference) === 0){
					response(204, "No Content");
				}
				foreach($reference as &$ref){
					$spec = $this->rm->selectWithDetail($ref['id']);
					if($spec !== false){
						$ref = array_merge($ref, $spec);
					}else{
						response(500, "Internal Server Error");
					}
				}
				response(200, "Reference from mark $mark", $reference);
			}else{
				response(500, "Internal Server Error");
			}
		}else{
			response(400, "Bad request");
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

	#[NoReturn] private function type_mark_ref(array $args){
		if(count($args['uri_args']) === 3){
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$type = $this->tm->select($args['uri_args'][0]);
		if(is_numeric($args['uri_args'][0])){
			$mark_name = $args['uri_args'][1];
			$type_name = $type['type'];
			$reference = $this->rm->selectAllByTypeMark((int)$type['id'], $mark_name, $iteration);
			foreach($reference as &$ref){
				$spec = $this->rm->selectWithDetail($ref['id']);
				$ref = array_merge($ref, $spec);
			}
			response(200, "Reference from mark $mark_name of type $type_name", $reference);
		}
	}

	public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type'){
				$this->type($args);
			}elseif($args['additional'][0] === 'type_product'){
				$this->product($args);
			}elseif($args['additional'][0] === 'type_reference'){
				$this->reference($args);
			}elseif($args['additional'][0] === 'mark_product'){
				$this->mark_prod($args);
			}elseif($args['additional'][0] === 'mark_reference'){
				$this->mark_ref($args);
			}elseif($args['additional'][0] === 'type_mark_reference'){
				$this->type_mark_ref($args);
			}
		}
	}

	public function post(array $args){
		// TODO: Implement post() method.
	}

	public function put(array $args){
		// TODO: Implement put() method.
	}

	public function delete(array $args){
		// TODO: Implement delete() method.
	}

}
