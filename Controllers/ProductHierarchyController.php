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
						response(200, "Product from type ${type['type']}", $reference);
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

	public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type'){
				$this->type($args);
			}elseif($args['additional'][0] === 'product'){
				$this->product($args);
			}elseif($args['additional'][0] === 'reference'){
				$this->reference($args);
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
