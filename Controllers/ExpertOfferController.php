<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class ExpertOfferController implements Controller{

	private OffersModel $om;
	private ProductModel $pm;
	private ReferenceModel $rm;
	private TypeModel $tm;
	private UserModel $um;
	private TokenModel $tkm;

	public function __construct(){
		$this->om = new OffersModel();
		$this->pm = new ProductModel();
		$this->rm = new ReferenceModel();
		$this->tm = new TypeModel();
		$this->um = new UserModel();
		$this->tkm = new TokenModel();
	}

	private function checkParameter($count, $order, $sort):bool{
		if(!is_numeric($count)){
			return false;
		}
		if($order !== "DESC" && $order !== "ASC"){
			return false;
		}
		if(!isset($this->om->column["$sort"]) && $sort !== "id"){
			return false;
		}
		return true;
	}

	private function getDetail(&$offers){
		if($offers === false){
			response(500, "Internal Server Error");
		}
		if(empty($offers)){
			response(204, "No Content");
		}
		foreach($offers as &$o){
			$prod = $this->pm->select($o['id'], "offer");
			if($prod === false){
				response(500, "Internal Server Error");
			}
			$ref = $this->rm->select($prod['ref']);
			if($ref === false){
				response(500, "Internal Server Error");
			}
			$type = $this->tm->select($ref['type']);
			if($type === false){
				response(500, "Internal Server Error");
			}
			$prod = $this->pm->selectWithDetail($prod['id']);
			if($prod === false){
				response(500, "Internal Server Error");
			}
			$o['brand'] = $prod['spec']['brand'];
			$o['model'] = $prod['spec']['model'];
			$o['type'] = $type['type'];
		}
	}

	#[NoReturn] private function getAll(array $args){
		$count = $args['uri_args'][1] ?? 0;
		$search = $args['uri_args'][2] ?? "";
		$order = strtoupper($args['uri_args'][3] ?? "ASC");
		$sort = $args['uri_args'][4] ?? "id";
		if(!$this->checkParameter($count,$order, $sort)){
			response(400, "Bad Request");
		}
		$offers = $this->om->selectAllFilterNotStarted($search, $order, $sort, $count);
		$this->getDetail($offers);
		$total = $this->om->selectTotalFilterNotStarted($search, $order, $sort);
		$totalNotFiltered = $this->om->selectTotalNotStarted();
		if($totalNotFiltered === false || $total === false){
			response(500, 'Internal Server Error');
		}
		$offers['total'] = $total;
		$offers['totalNotFiltered'] = $totalNotFiltered;
		response(200, "Offer Not Started", $offers);
	}

	#[NoReturn] public function getActive($args){
		$expert = getUser(new TokenModel(), $args['uri_args'][0], new UserModel());
		$count = $args['uri_args'][1] ?? 0;
		$search = $args['uri_args'][2] ?? "";
		$order = strtoupper($args['uri_args'][3] ?? "ASC");
		$sort = $args['uri_args'][4] ?? "id";
		if(!$this->checkParameter($count, $order, $sort)){
			response(400, "Bad Request");
		}
		$offers = $this->om->selectAllFilterActive($search, $order, $sort, $expert['id'], $count);
		$this->getDetail($offers);
		$total = $this->om->selectTotalFilterActive($search, $order, $sort, $expert['id']);
		$totalNotFiltered = $this->om->selectTotalActive($expert['id']);
		if($totalNotFiltered === false || $total === false){
			response(500, 'Internal Server Error');
		}
		$offers['total'] = $total;
		$offers['totalNotFiltered'] = $totalNotFiltered;
		response(200, "Offer Not Started", $offers);
	}

	#[NoReturn] public function getHistory(array $args){
		$expert = getUser(new TokenModel(), $args['uri_args'][0], new UserModel());
		$count = $args['uri_args'][1] ?? 0;
		$search = $args['uri_args'][2] ?? "";
		$order = strtoupper($args['uri_args'][3] ?? "ASC");
		$sort = $args['uri_args'][4] ?? "id";
		if(!$this->checkParameter($count, $order, $sort)){
			response(400, "Bad Request");
		}
		$offers = $this->om->selectAllFilterOld($search, $order, $sort, $expert['id'], $count);
		$this->getDetail($offers);
		$total = $this->om->selectTotalFilterOld($search, $order, $sort, $expert['id']);
		$totalNotFiltered = $this->om->selectTotalOld($expert['id']);
		if($totalNotFiltered === false || $total === false){
			response(500, 'Internal Server Error');
		}
		$offers['total'] = $total;
		$offers['totalNotFiltered'] = $totalNotFiltered;
		response(200, "Offer Not Started", $offers);
	}

	/**
	 * @param array $args
	 */
	#[NoReturn] public function get(array $args){
		if(!checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		if(!isset($args['additional'])){
			$this->getAll($args);
		}elseif($args['additional'][0] === "pending"){
			$this->getActive($args);
		}elseif($args['additional'][0] === "history"){
			$this->getHistory($args);
		}
	}

	/**
	 * @param array $args
	 */
	#[NoReturn] public function post(array $args){
		if(checkToken($args['uri_args'][0],3)){
			$expert = getUser($this->tkm, $args['uri_args'][0],$this->um);
			if(!is_numeric($args['uri_args'][1])){
				response(400,'Bad Request');
			}
			$offer = $this->om->select($args['uri_args'][1]);
			if($offer === false){
				response(404,"Not Found");
			}
			if((int)$offer['status'] !== 2){
				response(400,'Bad Request');
			}
			if($this->om->update($offer['id'],['expert'=>$expert['id'],'status'=>3])){
				response(200,"Offer Updated");
			}else{
				response(500,"Bah c'est le serveur qui plante");
			}
		}else{
			response(403,"Forbidden");
		}
	}
	/**
	 * @param array $args
	 */
	public function put(array $args){
		if(checkToken($args['uri_args'],3)){
			$expert = getUser($this->tkm,$args['uri_args'][0],$this->um);
			if(!isset($args['post_args']['counter_offer'],$args['post_args']['id'])){
				response(400,"Bad Request");
			}
			if(!is_numeric($args['post_args']['id']) || !is_numeric($args['post_args']['counter_offer'])){
				response(400,"Bad Request");
			}
			$offer = $this->om->select($args['post_args']['id']);
			if($offer !== false){
				response(404,"Not Found");
			}
			if((int)$offer['status'] !== 3){
				response(400,"Bad Request");
			}
			if($offer['expert'] !== $expert['id']){
				response(401,"Unauthorized");
			}

			if($this->om->update($offer['id'],['counter_offer'=>$args['post_args']['counter_offer'],'status'=>4])){
				response(200,"Offer Updated");
			}else{
				response(500,"Bah c'est le serveur qui plante");
			}
		}else{
			response(403,"For Bidden");
		}
	}

	/**
	 * @param array $args
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}