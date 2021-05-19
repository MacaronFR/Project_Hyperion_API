<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class TerminatedOfferController implements Controller{
	private OffersModel $om;
	private ProductModel $pm;
	private ReferenceModel $rm;
	private TypeModel $tm;
	private TokenModel $tom;
	public function __construct(){
		$this->om = new OffersModel();
		$this->pm = new ProductModel();
		$this->rm = new ReferenceModel();
		$this->tm = new TypeModel();
		$this->tom = new TokenModel();
	}

	#[NoReturn] public function getUserTerminated(array $args){
		if(!checkToken($args['uri_args'][0], 4)){
			response(403, "Forbidden");
		}
		$token = $this->tom->selectByToken($args['uri_args'][0]);
		$total = $this->om->selectTotalTerminatedByUser($token['user']);
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][1])){
			if(is_numeric($args['uri_args'][1])){
				$this->getTerminated($this->om->selectAllTerminatedByUser($token['user'], $args['uri_args'][1]), $total);
			}
		}
		$this->getTerminated($this->om->selectAllTerminatedByUser($token['user'], limit: false), $total);
	}

	#[NoReturn] public function getAllTerminated(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		$total = $this->om->selectTotalTerminated();
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][1])){
			if(is_numeric($args['uri_args'][1])){
				$this->getTerminated($this->om->selectAllTerminated($args['uri_args'][1]), $total);
			}
		}
		$this->getTerminated($this->om->selectAllTerminated(limit: false), $total);
	}

	#[NoReturn] public function getUserAdminTerminated(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		$total = $this->om->selectTotalTerminatedByUser($args['uri_args'][1]);
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][2])){
			if(is_numeric($args['uri_args'][2])){
				$this->getTerminated($this->om->selectAllTerminatedByUser($args['uri_args'][1], $args['uri_args'][2]), $total);
			}
		}
		$this->getTerminated($this->om->selectAllTerminatedByUser($args['uri_args'][1], limit: false), $total);
	}

	#[NoReturn] public function getTerminated(array|false $terminated, int $total){
		if($terminated === false){
			response(500, "Internal Server Error");
		}
		if(empty($terminated)){
			response(204, "No content");
		}
		foreach($terminated as &$p){
			$product = $this->pm->select($p['id'], "offer");
			if($product === false){
				response(500, "Internal Server Error");
			}
			$ref = $this->rm->select($product['ref']);
			if($ref === false){
				response(500, "Internal Server Error");
			}
			$spec = $this->rm->selectWithDetail($ref['id']);
			if($spec === false){
				response(500, "Internal Server Error");
			}
			$type = $this->tm->select($ref['type']);
			if($type === false){
				response(500, "Internal Server Error");
			}
			$p['type'] = $type['type'];
			$p['state'] = $product['state'];
			$p['brand'] = $spec['spec']['brand'];
			$p['model'] = $spec['spec']['model'];
		}
		$terminated['total'] = $terminated['totalNotFiltered'] = $total;
		response(200, "OK", $terminated);
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(!isset($args['additional'])){
			$this->getUserTerminated($args);
		}elseif($args['additional'][0] === 'all'){
			$this->getAllTerminated($args);
		}elseif($args['additional'][0] === 'user'){
			$this->getUserAdminTerminated($args);
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