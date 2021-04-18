<?php


namespace Hyperion\API;
require_once "autoload.php";


// get all types
class ProductHierarchyController implements Controller{

	/**
	 * execute Product Hierarchy on category and type
	 * @param array $args same as get() $args
	 */
	private function type(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
			$tm = new TypeModel();
			$cm = new CategoryModel();
			$cat = $cm->select($args['uri_args'][0]);
			if($cat !== false){
				$types = $tm->selectByCategory((int)$args['uri_args'][0], $iteration);
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

	private function product(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
			$pm = new ProductModel();
			$tm = new TypeModel();
			$type = $tm->select($args['uri_args'][0]);
			if($type !== false){
				$products = $pm->selectAllByType((int)$args['uri_args'][0], $iteration);
				if($products !== false){
					if(count($products) !== 0){
						foreach($products as &$prod){
							$spec = $pm->selectWithDetail($prod['id']);
							if($spec !== false){
								$prod = array_merge($prod, );
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

	public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type'){
				$this->type($args);
			}elseif($args['additional'][0] === 'product'){
				$this->product($args);
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
