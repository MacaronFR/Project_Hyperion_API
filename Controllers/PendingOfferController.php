<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class PendingOfferController implements Controller{

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

	#[NoReturn] public function getUserPending(array $args){
		if(!checkToken($args['uri_args'][0], 4)){
			response(403, "Forbidden");
		}
		$token = $this->tom->selectByToken($args['uri_args'][0]);
		$total = $this->om->selectTotalPendingByUser($token['user']);
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][1])){
			if(is_numeric($args['uri_args'][1])){
				$this->getPending($this->om->selectAllPendingByUser($token['user'], $args['uri_args'][1]), $total);
			}
		}
		$this->getPending($this->om->selectAllPendingByUser($token['user'], limit: false), $total);
	}

	#[NoReturn] public function getAllPending(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		$total = $this->om->selectTotalPending();
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][1])){
			if(is_numeric($args['uri_args'][1])){
				$this->getPending($this->om->selectAllPending($args['uri_args'][1]), $total);
			}
		}
		$this->getPending($this->om->selectAllPending(limit: false), $total);
	}

	#[NoReturn] public function getUserAdminPending(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		$total = $this->om->selectTotalPendingByUser($args['uri_args'][1]);
		if($total === false){
			response(500, "Internal Server Error");
		}
		if(isset($args['uri_args'][2])){
			if(is_numeric($args['uri_args'][2])){
				$this->getPending($this->om->selectAllPendingByUser($args['uri_args'][1], $args['uri_args'][2]), $total);
			}
		}
		$this->getPending($this->om->selectAllPendingByUser($args['uri_args'][1], limit: false), $total);
	}

	#[NoReturn] public function getPending(array|false $pending, int $total){
		if($pending === false){
			response(501, "Internal Server Error");
		}
		if(empty($pending)){
			response(204, "No content");
		}
		foreach($pending as &$p){
			$product = $this->pm->select($p['id'], "offer");
			if($product === false){
				response(502, "Internal Server Error");
			}
			$ref = $this->rm->select($product['ref']);
			if($ref === false){
				response(503, "Internal Server Error");
			}
			$spec = $this->rm->selectWithDetail($ref['id']);
			if($spec === false){
				response(504, "Internal Server Error");
			}
			$type = $this->tm->select($ref['type']);
			if($type === false){
				response(505, "Internal Server Error");
			}
			$p['type'] = $type['type'];
			$p['state'] = $product['state'];
			$p['brand'] = $spec['spec']['brand'][0][0];
			$p['model'] = $spec['spec']['model'][0][0];
		}
		$pending['total'] = $pending['totalNotFiltered'] = $total;
		response(200, "OK", $pending);
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(!isset($args['additional'])){
			$this->getUserPending($args);
		}elseif($args['additional'][0] === 'all'){
			$this->getAllPending($args);
		}elseif($args['additional'][0] === 'user'){
			$this->getUserAdminPending($args);
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