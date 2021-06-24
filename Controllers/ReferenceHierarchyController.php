<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ReferenceHierarchyController implements Controller{
	private ReferenceModel $rm;
	private SpecificationModel $sm;
	private RefHaveSpecModel $rhsm;
	private ProductModel $pm;
	private TypeModel $tm;

	public function __construct(){
		$this->rm = new ReferenceModel();
		$this->sm = new SpecificationModel();
		$this->rhsm = new RefHaveSpecModel();
		$this->pm = new ProductModel();
		$this->tm = new TypeModel();
	}

	#[NoReturn] public function ref($args, bool $detail = false){
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
			$result = $this->rm->selectAllFilter($search, $order, $sort, $page);
			$totalFilter = $this->rm->selectTotalFilter($search, $order, $sort);
			$total = $this->rm->selectTotal();
		}else{
			if(count($args['uri_args']) === 0){
				$result = $this->rm->selectAll(limit: false);
			}else{
				$result = $this->rm->selectAll($page);
			}
			$total = $this->rm->selectTotal();
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
				$spec = $this->rm->selectWithDetail($res['id']);
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
		response(200, "References $start to $end", $result);
	}

	#[NoReturn] private function type_reference(array $args){
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
		$reference = $this->rm->selectAllByType((int)$args['uri_args'][0], $iteration);
		if($reference === false){
			response(500, 'Internal Server Error');
		}
		if(count($reference) === 0){
			response(204, "No Content");
		}
		response(200, "Reference from type " . $reference[0]['type'], $reference);
	}

	#[NoReturn] private function brand_ref(array $args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$reference = $this->rm->selectAllByBrand($args['uri_args'][0], $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(count($reference) === 0){
			response(204, "No Content");
		}
		response(200, "Reference of brand " . $reference[0]['brand'], $reference);
	}

	#[NoReturn] private function type_brand_ref(array $args){
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$reference = $this->rm->selectAllByTypeBrand((int)$args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of type " . $reference[0]['type'] . ", brand " . $reference[0]['brand'], $reference);
	}

	#[NoReturn] private function model_reference($args){
		$reference = $this->rm->selectByModel($args['uri_args'][0]);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of model " . $reference['model'], $reference);
	}

	#[NoReturn] public function brand_model_reference(array $args){
		$reference = $this->rm->selectByBrandModel($args['uri_args'][0], $args['uri_args'][1]);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of brand " . $reference['brand'] . ", model " . $reference['model'], $reference);
	}

	#[NoReturn] private function type_model_reference($args){
		$reference = $this->rm->selectByTypeModel($args['uri_args'][0], $args['uri_args'][1]);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of type " . $reference['type'] . ", model " . $reference['model'], $reference);
	}

	#[NoReturn] public function type_brand_model_reference(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if($this->tm->select($args['uri_args'][0]) === false){
			response(404, "Type Not Found");
		}
		if($this->sm->selectBrand($args['uri_args'][1]) === false){
			response(404, "Brand Not Found");
		}
		if($this->sm->selectModel($args['uri_args'][2]) === false){
			response(404, "Model not Found");
		}
		$references = $this->rm->selectByTypeBrandModel($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2]);
		if($references === false){
			response(500, "Internal Server Error");
		}
		if(empty($references)){
			response(204, "No Content");
		}
		response(200, "Reference of type " . $references['type'] . ", brand " . $references["brand"] . ", model " . $references['model'], $references);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if($args['additional'][0] === 'type_reference'){
			$this->type_reference($args);
		}elseif($args['additional'][0] === 'brand_reference'){
			$this->brand_ref($args);
		}elseif($args['additional'][0] === 'type_brand_reference'){
			$this->type_brand_ref($args);
		}elseif($args['additional'][0] === 'model_reference'){
			$this->model_reference($args);
		}elseif($args['additional'][0] === 'brand_model_reference'){
			$this->brand_model_reference($args);
		}elseif($args['additional'][0] === 'type_model_reference'){
			$this->type_model_reference($args);
		}elseif($args['additional'][0] === 'type_brand_model_reference'){
			$this->type_brand_model_reference($args);
		}elseif($args['additional'][0] === 'ref'){
			$this->ref($args);
		}elseif($args['additional'][0] === 'ref_detail'){
			$this->ref($args, true);
		}
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!isset($args['post_args']['type'], $args['post_args']['brand'], $args['post_args']['model'], $args['post_args']['specs'], $args['post_args']['buying'], $args['post_args']['selling'])){
			response(400, "Bad request");
		}
		if(!is_numeric($args['post_args']['type']) || !is_numeric($args['post_args']['brand']) || !is_numeric($args['post_args']['selling']) || !is_numeric($args['post_args']['buying'])){
			response(400, "Bad Request");
		}
		if(!is_array($args['post_args']['specs'])){
			response(400, "Bad Request");
		}
		$brand = $this->sm->select($args['post_args']['brand']);
		if($brand === false){
			response(400, "Bad Request, Brand not found");
		}
		$specs = [];
		foreach($args['post_args']['specs'] as $spec){
			if(!isset($spec['name'], $spec['value']) || !is_array($spec['value'])){
				response(400, "Bad Request");
			}
			$specs[] = ["name" => $spec['name'], "value" => $spec['value']];
		}
		$exist = $this->rm->selectByTypeBrandModel($args['post_args']['type'], $brand['value'], $args['post_args']['model']);
		if($exist !== false){
			response(209, "Conflict");
		}
		$val = array_intersect_key($args['post_args'], ["type" => 0, "buying" => 0, "selling" => 0]);
		$new_ref = $this->rm->insert($val);
		if($new_ref === false){
			response(500, "Internal Server Error");
		}
		$model_exist = $this->sm->selectIdentical(["name" => "model", "value" => $args['post_args']['model']]);
		if($model_exist === false){
			$model = $this->sm->insert(["name" => "model", "value" => $args['post_args']['model']]);
		}else{
			$model = $model_exist['id'];
		}
		if($this->rhsm->insert(["product" => $new_ref , "spec" => $model]) === false){
			response(500, "Internal Server Error");
		}
		if($this->rhsm->insert(["product" => $new_ref , "spec" => $args['post_args']['brand']]) === false){
			response(500, "Internal Server Error");
		}
		foreach($specs as $spec){
			foreach($spec['value'] as $val){
				$spec_id = $this->sm->selectIdentical(["name" => $spec['name'], "value" => $val['value']]);
				if($spec_id === false){
					$spec_id = $this->sm->insert(["name" => $spec['name'], "value" => $val['value']]);
					if($spec_id === false){
						response(500, "Internal Server Error");
					}
				}else{
					$spec_id = $spec_id['id'];
				}
				if($this->rhsm->insert(['product' => $new_ref, 'spec' => $spec_id, 'bonus' => $val['bonus']]) === false){
					response(500, "Internal Server Error");
				}
			}
		}
		response(201, "Reference created");
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
		$ref = $this->rm->select($args['uri_args'][1]);
		if($ref === false){
			response(404, "Not Found");
		}
		$prod = $this->pm->selectAllByRef($args['uri_args'][1]);
		if($prod === false){
			response(500, "Internal Server Error");
		}
		if(!empty($prod)){
			response(409, "Conflict");
		}
		$have_spec = $this->rhsm->selectAllFromRef($args['uri_args'][1]);
		if($have_spec === false){
			response(500, "Internal Server Error");
		}
		if(!empty($have_spec)){
			if(!$this->rhsm->deleteFromRef($args['uri_args'][1])){
				response(501, "Internal Server Error");
			}
		}
		if($this->rm->delete($args['uri_args'][1])){
			response(204, "Deleted");
		}
		response(500, "Internal Server Error");
	}
}