<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ShopController implements Controller{

	private ProductModel $pm;
	private SpecificationModel $sm;
	private TypeModel $tm;
	private CategoryModel $cm;

	public function __construct(){
		$this->pm = new ProductModel();
		$this->sm = new SpecificationModel();
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
	}

	private function stringToFilterArray(string $filter): array{
		$each = explode("/", $filter);
		if(count($each) % 2 !== 0){
			response(400, "Bad Request");
		}
		$array = [];
		for($i = 0; $i < count($each); $i += 2){
			$array[] = [$each[$i], $each[$i+1]];
		}
		return $array;
	}

	private function checkFilter(array $filter){
		foreach($filter as $f){
			$exist = $this->sm->selectIdentical(['name' => $f[0], 'value' => $f[1]]);
			if($exist === false){
				response(404, "Specification not found");
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if($args['additional'][0] === "main"){
			$this->getMain($args);
		}elseif($args['additional'][0] === "brand"){
			$this->getBrand($args);
		}elseif($args['additional'][0] === "type"){
			$this->getType($args);
		}elseif($args['additional'][0] === "cat"){
			$this->getCat($args);
		}
	}

	#[NoReturn] private function getCat(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if($this->cm->select($args['uri_args'][0]) === false){
			response(404, "Type Not Found");
		}
		if(($args['additional'][1] ?? "") === "type"){
			$type = $args['uri_args'][1];
			if(!is_numeric($type)){
				response(400, "Bad Request");
			}
			if($this->tm->select($type) === false){
				response(404, "Type Not Found");
			}
			if(isset($args['additional'][2])){
				$brand = $args['uri_args'][2];
				if($this->sm->selectIdentical(['name' => "brand", 'value' => $brand]) === false){
					response(404, "Brand not found");
				}
				$iteration = $args['uri_args'][3];
				if(count($args['uri_args']) === 5){
					$filter = $this->stringToFilterArray($args['uri_args'][4]);
					$this->checkFilter($filter);
				}else{
					$filter = [];
				}
			}else{
				$brand = "";
				$iteration = $args['uri_args'][2];
				if(count($args['uri_args']) === 4){
					$filter = $this->stringToFilterArray($args['uri_args'][3]);
					$this->checkFilter($filter);
				}else{
					$filter = [];
				}
			}
		}else{
			$type = -1;
			if(isset($args['additional'][1])){
				$brand = $args['uri_args'][1];
				if($this->sm->selectIdentical(['name' => "brand", 'value' => $brand]) === false){
					response(404, "Brand not found");
				}
				$iteration = $args['uri_args'][2];
				if(count($args['uri_args']) === 4){
					$filter = $this->stringToFilterArray($args['uri_args'][3]);
					$this->checkFilter($filter);
				}else{
					$filter = [];
				}
			}else{
				$brand = "";
				$iteration = $args['uri_args'][1];
				if(count($args['uri_args']) === 3){
					$filter = $this->stringToFilterArray($args['uri_args'][2]);
					$this->checkFilter($filter);
				}else{
					$filter = [];
				}
			}
		}
		$products = $this->pm->selectShop($args['uri_args'][0], $type, $brand, $filter, iteration: $iteration);
		$this->processProduct($products);
		$total = $this->pm->selectShopTotal($args['uri_args'][0], $type, $brand, $filter);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$products['total'] = (int)$total['total'];
		response(200, "Shop", $products);
	}

	#[NoReturn] private function getType(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if($this->tm->select($args['uri_args'][0]) === false){
			response(404, "Type Not Found");
		}
		if(isset($args['additional'][1]) && $args['additional'][1] === "brand"){
			$brand = $args['uri_args'][1];
			if($this->sm->selectIdentical(['name' => "brand", 'value' => $brand]) === false){
				response(404, "Brand not found");
			}
			$iteration = $args['uri_args'][2];
			if(count($args['uri_args']) === 4){
				$filter = $this->stringToFilterArray($args['uri_args'][3]);
				$this->checkFilter($filter);
			}else{
				$filter = [];
			}
		}else{
			$brand = "";
			$iteration = $args['uri_args'][1];
			if(count($args['uri_args']) === 3){
				$filter = $this->stringToFilterArray($args['uri_args'][2]);
				$this->checkFilter($filter);
			}else{
				$filter = [];
			}
		}
		$products = $this->pm->selectShop(type: $args['uri_args'][0], brand: $brand, filter: $filter, iteration: $iteration);
		$this->processProduct($products);
		$total = $this->pm->selectShopTotal(type: $args['uri_args'][0], brand: $brand, filter: $filter);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$products['total'] = (int)$total['total'];
		response(200, "Shop", $products);
	}

	#[NoReturn] private function getBrand(array $args){
		if(count($args['uri_args']) === 3){
			$filter = $this->stringToFilterArray($args['uri_args'][2]);
			$this->checkFilter($filter);
		}else{
			$filter = [];
		}
		$products = $this->pm->selectShop(brand: $args['uri_args'][0], filter: $filter, iteration: $args['uri_args'][1]);
		$this->processProduct($products);
		$total = $this->pm->selectShopTotal(brand: $args['uri_args'][0], filter: $filter);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$products['total'] = $total['total'];
		response(200, "Shop", $products);
	}

	#[NoReturn] private function getMain(array $args){
		if(count($args['uri_args']) === 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			$prod = $this->pm->selectAllFilter("", "DESC", "buy_d", $args['uri_args'][0]);
		}else{
			$prod = $this->pm->selectAllFilter("", "DESC", "buy_d", limit: false);
		}
		if($prod === false){
			response(500, "Internal Server Error");
		}
		if(empty($prod)){
			response(204, "No content");
		}
		$total = $this->pm->selectTotal();
		if($total === false){
			response(500, "Internal Server Error");
		}
		$prod['total'] = $prod['totalNotFiltered'] = $total;
		response(200, "OK", $prod);
	}

	private function processProduct(array|false &$products){
		if($products === false){
			response(500, "Internal Server Error");
		}
		if(empty($products)){
//			response(204, "No Content");
		}
		foreach($products as &$p){
			$detail = $this->pm->selectWithDetail($p['id']);
			if($detail === false){
				response(500, "Internal Server Error");
			}
			$p = array_merge($p, $detail['spec']);
		}
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