<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ReferenceHierarchyController implements Controller{
	private TypeModel $tm;
	private ReferenceModel $rm;

	public function __construct(){
		$this->tm = new TypeModel();
		$this->rm = new ReferenceModel();
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

	#[NoReturn] private function mark_ref(array $args){
		if(count($args['uri_args']) === 2){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		$reference = $this->rm->selectAllByMark($args['uri_args'][0], $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(count($reference) === 0){
			response(204, "No Content");
		}
		response(200, "Reference of mark " . $reference[0]['mark'], $reference);
	}

	#[NoReturn] private function type_mark_ref(array $args){
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
		$reference = $this->rm->selectAllByTypeMark((int)$args['uri_args'][0], $args['uri_args'][1], $iteration);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of type " . $reference[0]['type'] . ", mark " . $reference[0]['mark'], $reference);
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

	#[NoReturn] public function mark_model_reference(array $args){
		$reference = $this->rm->selectByMarkModel($args['uri_args'][0], $args['uri_args'][1]);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		if(empty($reference)){
			response(204, "No content");
		}
		response(200, "Reference of mark " . $reference['mark'] . ", model " . $reference['model'], $reference);
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

	#[NoReturn] public function type_mark_model_reference(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		$references = $this->rm->selectByTypeMarkModel($args['uri_args'][0], $args['uri_args'][1], $args['uri_args'][2]);
		if($references === false){
			response(500, "Internal Server Error");
		}
		if(empty($references)){
			response(204, "No Content");
		}
		response(200, "Reference of type " . $references['type'] . ", mark " . $references["mark"] . ", model " . $references['model'], $references);
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if($args['additional'][0] === 'type_reference'){
			$this->type_reference($args);
		}elseif($args['additional'][0] === 'mark_reference'){
			$this->mark_ref($args);
		}elseif($args['additional'][0] === 'type_mark_reference'){
			$this->type_mark_ref($args);
		}elseif($args['additional'][0] === 'model_reference'){
			$this->model_reference($args);
		}elseif($args['additional'][0] === 'mark_model_reference'){
			$this->mark_model_reference($args);
		}elseif($args['additional'][0] === 'type_model_reference'){
			$this->type_model_reference($args);
		}elseif($args['additional'][0] === 'type_mark_model_reference'){
			$this->type_mark_model_reference($args);
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