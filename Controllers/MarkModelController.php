<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class MarkModelController implements Controller{

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

	#[NoReturn] private function markByType(array $args){
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
			$marks = $this->rm->selectAllMarkType((int)$args['uri_args'][0], $args['uri_args'][1]);
		}else{
			$marks = $this->rm->selectAllMarkType((int)$args['uri_args'][0], limit: false);
		}
		if($marks === false){
			response(500, "Internal Server Error");
		}
		if(count($marks) === 0){
			response(204, "No content");
		}
		foreach($marks as &$mark){
			unset($mark['id_product']);
		}
		$marks['totalNotFiltered'] = $marks['total'] = count($marks);
		response(200, "Mark of type " . $type['type'], $marks);
	}

	#[NoReturn] public function modelByMark(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = $args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$models = $this->rm->selectAllModelByMark($args['uri_args'][0], $iteration);
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No Content");
		}
		response(200, "Models of mark " . $args['uri_args'][0], $models);
	}

	#[NoReturn] public function modelByTypeMark(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if(count($args['uri_args']) === 3){
			if(!is_numeric($args['uri_args'][2])){
				response(400, "Bad Request");
			}
			$models = $this->sm->selectAllModelByTypeMark($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2]);
		}else{
			$models = $this->sm->selectAllModelByTypeMark($args['uri_args'][0], $args['uri_args'][1], limit: false);
		}
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No Content");
		}
		response(200, "Models of mark " . $args['uri_args'][1], $models);
	}

	#[NoReturn] public function mark($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$mark = $this->sm->selectAllMark($args['uri_args'][1]);
		}else{
			$mark = $this->sm->selectAllMark(limit: false);
		}
		if($mark === false){
			response(500, "Internal Server Error");
		}
		if(empty($mark)){
			response(204, "No content");
		}
		response(200, "Marks", $mark);
	}

	#[NoReturn] public function model($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = $args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$model = $this->rm->selectAllModel($iteration);
		if($model === false){
			response(500, "Internal Server Error");
		}
		if(empty($model)){
			response(204, "No content");
		}
		response(200, "Models", $model);
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function get(array $args){
		if($args['additional'][0] === 'type'){
			$this->type($args);
		}elseif($args['additional'][0] === "type_mark"){
			$this->markByType($args);
		}elseif($args['additional'][0] === "mark_model"){
			$this->modelByMark($args);
		}elseif($args['additional'][0] === "type_mark_model"){
			$this->modelByTypeMark($args);
		}elseif($args['additional'][0] === "mark"){
			$this->mark($args);
		}elseif($args['additional'][0] === "model"){
			$this->model($args);
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
		return false;
	}
}