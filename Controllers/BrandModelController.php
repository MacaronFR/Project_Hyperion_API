<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class BrandModelController implements Controller{

	private ReferenceModel $rm;
	private TypeModel $tm;
	private CategoryModel $cm;
	private SpecificationModel $sm;

	public function __construct(){
		$this->sm = new SpecificationModel();
		$this->rm = new ReferenceModel();
		$this->tm = new TypeModel();
		$this->cm = new CategoryModel();
	}

	#[NoReturn] private function type(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$cat = $this->cm->select($args['uri_args'][0]);
		if($cat === false){
			response(404, "Category Not Found");
		}
		if(count($args['uri_args']) === 2){
			$types = $this->tm->selectByCategory((int)$args['uri_args'][0], (int)$args['uri_args'][1]);
		}else{
			$types = $this->tm->selectByCategory((int)$args['uri_args'][0], limit: false);
		}
		if($types === false){
			response(500, 'Error retrieving types');
		}
		if(count($types) !== 0){
			$types['total'] = $this->tm->selectTotalByCategory((int)$args['uri_args'][0]);
			$types['totalNotFiltered'] = $types['total'];
			response(200, "Type from category ${cat['name']}", $types);
		}
		response(204, "No Content");
	}

	#[NoReturn] private function brandByType(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$type = $this->tm->select((int)$args['uri_args'][0]);
		if($type === false){
			response(500, "Internal Server Error");
		}
		if(empty($type)){
			response(400, "Invalid Type");
		}
		if(count($args['uri_args']) === 2){
			$brands = $this->rm->selectAllBrandType((int)$args['uri_args'][0], $args['uri_args'][1]);
		}else{
			$brands = $this->rm->selectAllBrandType((int)$args['uri_args'][0], limit: false);
		}
		if($brands === false){
			response(500, "Internal Server Error");
		}
		if(count($brands) === 0){
			response(204, "No content");
		}
		foreach($brands as &$brand){
			unset($brand['id_product']);
		}
		$brands['totalNotFiltered'] = $brands['total'] = count($brands);
		response(200, "Brand of type " . $type['type'], $brands);
	}

	#[NoReturn] public function modelByBrand(array $args){
		if(count($args['uri_args']) === 2){
			$models = $this->rm->selectAllModelByBrand($args['uri_args'][0], $args['uri_args'][1]);
		}else{
			$models = $this->rm->selectAllModelByBrand($args['uri_args'][0], limit: false);
		}
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No Content");
		}
		$total = $this->rm->selectTotalModelByBrand($args['uri_args'][0]);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$models['total'] = $models['totalNotFiltered'] = (int)$total['count'];
		response(200, "Models of brand " . $args['uri_args'][0], $models);
	}

	#[NoReturn] public function modelByTypeBrand(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$models = $this->sm->selectAllModelByTypeBrand($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2]);
		}else{
			$models = $this->sm->selectAllModelByTypeBrand($args['uri_args'][0], $args['uri_args'][1], limit: false);
		}
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No Content");
		}
		$total = $this->sm->selectTotalModelByTypeBrand($args['uri_args'][0], $args['uri_args'][1]);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$models['total'] = $models['totalNotFiltered'] = $total;
		response(200, "Models of brand " . $args['uri_args'][1], $models);
	}

	#[NoReturn] public function brand($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$brand = $this->sm->selectAllBrand($args['uri_args'][1]);
		}else{
			$brand = $this->sm->selectAllBrand(limit: false);
		}
		if($brand === false){
			response(500, "Internal Server Error");
		}
		if(empty($brand)){
			response(204, "No content");
		}
		$total = $this->sm->selectTotalBrand();
		if($total === false){
			response(500, "Internal Server Error");
		}
		$brand['totalNotFiltered'] = $brand['total'] = (int)$total['count'];
		response(200, "Brands", $brand);
	}

	#[NoReturn] public function model($args){
		if(count($args['uri_args']) === 1){
			if(!is_numeric($args['uri_args'][0])){
				response(400, "Bad Request");
			}
			$models = $this->sm->selectAllModel($args['uri_args'][0]);
		}else{
			$models = $this->sm->selectAllModel(limit: false);
		}
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No content");
		}
		$total = $this->sm->selectTotalModel();
		if($total === false){
			response(500, "Internal Server Error");
		}
		$models['total'] = $models['totalNotFiltered'] = $total;
		response(200, "Models", $models);
	}

	#[NoReturn] public function type_model(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$type = $this->tm->select((int)$args['uri_args'][0]);
		if($type === false){
			response(500, "Internal Server Error");
		}
		if(empty($type)){
			response(400, "Invalid Type");
		}
		if(count($args['uri_args']) === 2){
			$models = $this->sm->selectAllModelByType((int)$args['uri_args'][0], $args['uri_args'][1]);
		}else{
			$models = $this->sm->selectAllModelByType((int)$args['uri_args'][0], limit: false);
		}
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(count($models) === 0){
			response(204, "No content");
		}
		$total = $this->sm->selectTotalModelByType($args['uri_args'][0]);
		if($total === false){
			response(500, "Internal Server Error");
		}
		$models['totalNotFiltered'] = $models['total'] = $total['count'];
		response(200, "Brand of type " . $type['type'], $models);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if($args['additional'][0] === 'type'){
			$this->type($args);
		}elseif($args['additional'][0] === "type_brand"){
			$this->brandByType($args);
		}elseif($args['additional'][0] === "brand_model"){
			$this->modelByBrand($args);
		}elseif($args['additional'][0] === "type_brand_model"){
			$this->modelByTypeBrand($args);
		}elseif($args['additional'][0] === "brand"){
			$this->brand($args);
		}elseif($args['additional'][0] === "model"){
			$this->model($args);
		}elseif($args['additional'][0] === "type_model"){
			$this->type_model($args);
		}elseif($args['additional'][0] === 'brandcat'){
			$this->brandByCat($args);
		}
	}

	#[NoReturn] private function brandByCat(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$category = $this->cm->select((int)$args['uri_args'][0]);
		if($category === false){
			response(500, "Internal Server Error");
		}
		if(empty($category)){
			response(400, "Invalid Brand");
		}
		if(count($args['uri_args']) === 2){
			$brands = $this->rm->selectAllBrandType((int)$args['uri_args'][0], $args['uri_args'][1]);
		}else{
			$brands = $this->rm->selectAllBrandType((int)$args['uri_args'][0], limit: false);
		}
		if($brands === false){
			response(500, "Internal Server Error");
		}
		if(count($brands) === 0){
			response(204, "No content");
		}
		foreach($brands as &$brand){
			unset($brand['id_product']);
		}
		$brands['totalNotFiltered'] = $brands['total'] = count($brands);
		response(200, "Brand of category " . $category['name'], $brands);
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