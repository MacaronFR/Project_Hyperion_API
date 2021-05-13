<?php


namespace Hyperion\API;

use JetBrains\PhpStorm\NoReturn;

require_once "autoload.php";


// get all types
class ProductHierarchyController implements Controller{
	private ProductModel $pm;
	private ProductSpecModel $psm;

	public function __construct(){
		$this->pm = new ProductModel();
		$this->psm = new ProductSpecModel();
	}

	#[NoReturn] private function prod(array $args, bool $detail = false){
		$page = 0;
		if(count($args['uri_args']) >= 1){
			if(is_numeric($args['uri_args'][0])){
				$page = (int)$args['uri_args'][0];
			}else{
				response(400, "Bad Request");
			}
		}
		if(count($args['uri_args']) > 1){
			$order = $args['uri_args'][2] ?? 'ASC';
			$order = strtoupper($order);
			if($order !== "ASC" && $order !== "DESC"){
				response(409, "Bad Request");
			}
			$search = $args['uri_args'][1];
			$sort = $args['uri_args'][3] ?? 'id';
			$result = $this->pm->selectAllFilter($search, $order, $sort, $page);
			$totalFilter = $this->pm->selectTotalFilter($search, $order, $sort);
			$total = $this->pm->selectTotal();
		}else{
			if(count($args['uri_args']) === 0){
				$result = $this->pm->selectAll(limit: false);
			}else{
				$result = $this->pm->selectAll($page);
			}
			$total = $this->pm->selectTotal();
			$totalFilter = $total;
		}
		if($result === false){
			response(500, "Internal Server Error");
		}
		if(empty($result)){
			response(204, "No content");
		}
		if($total === false || $totalFilter === false){
			response(501, "Internal Server Error");
		}
		if($detail){
			foreach($result as &$res){
				$spec = $this->pm->selectWithDetail($res['id']);
				if($spec === false){
					response(500, "Internal Server Error");
				}
				if(!empty($spec)){
					$res = array_merge($res, $spec['spec']);
				}
			}
		}
		$start = $page * 10 + 1;
		if(count($args['uri_args']) === 0){
			$end = $totalFilter;
		}else{
			$end = ($page + 1) * 10;
		}
		$result['total'] = $totalFilter;
		$result['totalNotFiltered'] = $total;
		response(200, "Product $start to $end", $result);
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


	#[NoReturn] private function brand_prod(array $args){
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
		$brand = $args['uri_args'][0];
		$reference = $this->pm->selectAllByBrand($brand, $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No Content");
		}
		response(200, "Product of brand $brand", $reference);
	}

	#[NoReturn] private function type_brand_prod($args){
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
		$prod = $this->pm->selectAllByTypeBrand((int)$args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($prod === false){
			response(500, "Internal Server Error");
		}
		if(empty($prod)){
			response(204, "No content");
		}
		response(200, "Product of type " . $prod[0]['type'] . ", brand " . $prod[0]['brand'], $prod);
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

	#[NoReturn] public function brand_model_prod(array $args){
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$products = $this->pm->selectAllByBrandModel($args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($products === false){
			response(500, "Internal Server Error");
		}
		if(empty($products)){
			response(204, "No content");
		}
		response(200, "Product of brand , model ", $products);
	}

	#[NoReturn] public function type_brand_model_prod(array $args){
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
		$product = $this->pm->selectAllByTypeBrandModel($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2], $iteration);
		if($product === false){
			response(500, "Internal Server Error");
		}
		if(empty($product)){
			response(204, "No Content");
		}
		response(200, "Product of type " . $product[0]['type'] . ", brand " . $product[0]["brand"] . ", model " . $product[0]['model'], $product);
	}

	#[NoReturn] public function get(array $args){
		if(isset($args['additional'])){
			if($args['additional'][0] === 'type_product'){
				$this->type_product($args);
			}elseif($args['additional'][0] === 'brand_product'){
				$this->brand_prod($args);
			}elseif($args['additional'][0] === 'type_brand_product'){
				$this->type_brand_prod($args);
			}elseif($args['additional'][0] === 'model_product'){
				$this->model_prod($args);
			}elseif($args['additional'][0] === 'brand_model_product'){
				$this->brand_model_prod($args);
			}elseif($args['additional'][0] === 'type_model_product'){
				$this->type_model_prod($args);
			}elseif($args['additional'][0] === 'type_brand_model_product'){
				$this->type_brand_model_prod($args);
			}elseif($args['additional'][0] === "prod"){
				$this->prod($args);
			}elseif($args['additional'][0] === "prod_detail"){
				$this->prod($args, true);
			}
		}
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
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$product = $this->pm->select($args['uri_args'][1]);
		if($product === false){
			response(404, "Not Found");
		}
		$pspec = $this->psm->selectAllByProduct($product['id'], limit: false);
		if($pspec === false){
			response(500, "Internal Server Error");
		}
		foreach($pspec as $id){
			if(!$this->psm->delete($id['id'])){
				response(500, "Internal Server Error");
			}
		}
		if(!$this->pm->delete($product['id'])){
			response(500, "Internal Server Error");
		}
		response(204, "Product Deleted");
	}

}
