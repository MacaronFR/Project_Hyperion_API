<?php


namespace Hyperion\API;


class MarkModelController implements Controller{

	private ReferenceModel $rm;
	private TypeModel $tm;

	public function __construct(){
		$this->rm = new ReferenceModel();
		$this->tm = new TypeModel();
	}
	/**
	 * @inheritDoc
	 */
	public function get(array $args){
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