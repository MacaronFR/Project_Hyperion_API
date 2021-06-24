<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ReceptionOfferController implements Controller{

	private OffersModel $om;
	private ProductModel $pm;
	private TypeModel $tm;
	private ReferenceModel $rm;

	public function __construct(){
		$this->om = new OffersModel();
		$this->pm = new ProductModel();
		$this->tm = new TypeModel();
		$this->rm = new ReferenceModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(isset($args['uri_args'][1])){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$page = (int)$args['uri_args'][1];
		}else{
			$page = 0;
		}
		$total = $this->om->selectTotalReception();
		if($total === false){
			response(500, "Internal Server Error");
		}
		$this->getPending($this->om->selectAllFilterReception("", "ASC", "id", $page), $total);

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
	public function post(array $args){
		// TODO: Implement post() method.
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$user = getUser(new TokenModel(), $args['uri_args'][0], new UserModel());
		if((int)$user['status'] > 3){
			response(403, "Forbidden");
		}
		$offer = $this->om->select($args['uri_args'][1]);
		if($offer === false){
			response(404, "Not Found");
		}
		if($this->om->update($offer['id'], ['status' => 2])){

		}
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}