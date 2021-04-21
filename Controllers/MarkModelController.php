<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class MarkModelController implements Controller{

	private ReferenceModel $rm;
	private TypeModel $tm;

	public function __construct(){
		$this->rm = new ReferenceModel();
		$this->tm = new TypeModel();
	}

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

	#[NoReturn] private function markByType(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = $args['uri_args'][1];
		}else{
			$iteration = 0;
		}
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
		$marks = $this->rm->selectAllMarkType((int)$args['uri_args'][0], $iteration);
		if($marks === false){
			response(500, "Internal Server Error");
		}
		if(count($marks) === 0){
			response(204, "No content");
		}
		foreach($marks as &$mark){
			unset($mark['id_product']);
		}
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
		if(count($args['uri_args']) === 3){
			$iteration = $args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$models = $this->rm->selectAllModelByTypeMark($args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($models === false){
			response(500, "Internal Server Error");
		}
		if(empty($models)){
			response(204, "No Content");
		}
		response(200, "Models of mark " . $args['uri_args'][1], $models);
	}

	public function mark($args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = $args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$mark = $this->rm->selectAllMark($iteration);
		if($mark === false){
			response(500, "Internal Server Error");
		}
		if(empty($mark)){
			response(204, "No content");
		}
		response(200, "Marks", $mark);
	}

	public function model($args){
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
	public function get(array $args){
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