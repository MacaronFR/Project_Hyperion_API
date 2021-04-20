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
						response(200, "Reference from type ${type['type']}", $reference);
					}else{
						response(204, "No Content");
					}
				}else{
					response(500, 'Error retrieving reference');
				}
			}else{
				response(404, "Type Not Found");
			}
		}else{
			response(400, "Bad Request");
		}
	}

	#[NoReturn] private function mark_ref(array $args){
		if(count($args['uri_args']) === 2){
			$iteration = (int)$args['uri_args'][1];
		}else{
			$iteration = 0;
		}
		if(isset($args['uri_args'][0])){
			$mark = $args['uri_args'][0];
			$reference = $this->rm->selectAllByMark($mark, $iteration);
			if($reference !== false){
				if(count($reference) === 0){
					response(204, "No Content");
				}
				foreach($reference as &$ref){
					$spec = $this->rm->selectWithDetail($ref['id']);
					if($spec !== false){
						$ref = array_merge($ref, $spec);
					}else{
						response(500, "Internal Server Error");
					}
				}
				response(200, "Reference from mark $mark", $reference);
			}else{
				response(500, "Internal Server Error");
			}
		}else{
			response(400, "Bad request");
		}
	}

	#[NoReturn] private function type_mark_ref(array $args){
		if(count($args['uri_args']) === 3){
			$iteration = (int)$args['uri_args'][2];
		}else{
			$iteration = 0;
		}
		$type = $this->tm->select($args['uri_args'][0]);
		if(is_numeric($args['uri_args'][0])){
			$mark_name = $args['uri_args'][1];
			$type_name = $type['type'];
			$reference = $this->rm->selectAllByTypeMark((int)$type['id'], $mark_name, $iteration);
			foreach($reference as &$ref){
				$spec = $this->rm->selectWithDetail($ref['id']);
				$ref = array_merge($ref, $spec);
			}
			response(200, "Reference from mark $mark_name of type $type_name", $reference);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if($args['additional'][0] === 'type_reference'){
			$this->reference($args);
		}elseif($args['additional'][0] === 'mark_reference'){
			$this->mark_ref($args);
		}elseif($args['additional'][0] === 'type_mark_reference'){
			$this->type_mark_ref($args);
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