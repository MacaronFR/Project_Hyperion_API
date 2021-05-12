<?php


namespace Hyperion\API;

require_once "autoload.php";

use JetBrains\PhpStorm\NoReturn;

class OfferController implements Controller{

	private CategoryModel $cm;
	private SpecificationModel $sm;
	private TypeModel $tm;
	private ReferenceModel $rm;
	private OffersModel $om;
	private RefHaveSpecModel $rhsm;
	private ProductSpecModel $psm;
	private StateModel $stm;
	private ProductModel $pm;
	private TokenModel $tom;

	public function __construct(){
		$this->cm = new CategoryModel();
		$this->sm = new SpecificationModel();
		$this->tm = new TypeModel();
		$this->rm = new ReferenceModel();
		$this->om = new OffersModel();
		$this->rhsm = new RefHaveSpecModel();
		$this->psm = new ProductSpecModel();
		$this->stm = new StateModel();
		$this->pm = new ProductModel();
		$this->tom = new TokenModel();
	}

	#[NoReturn] public function getOfferById(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$offer = $this->om->select($args['uri_args'][1]);
		if($offer === false){
			response(500, "Internal Server Error");
		}
		if($offer['user'] !== $args['additional']['user'] && !checkToken($args['uri_args'][0], 3)){
			response(403, "Forbidden");
		}
		$product = $this->pm->select($offer['id'], "offer");
		if($product === false){
			response(500, "Internal Server Error");
		}
		$prod_detail = $this->pm->selectWithDetail($product['id']);
		if($prod_detail === false){
			response(500, "Internal Server Error");
		}
		$reference = $this->rm->select($product['ref']);
		if($reference === false){
			response(500, "Internal Server Error");
		}
		$type = $this->tm->select($reference['type']);
		if($type === false){
			response(500, "Internal ServeR Error");
		}
		$res = [
			'id_offer' => $offer['id'],
			'offer' => $offer['offer'],
			'counter_offer' => $offer['counter_offer'],
			'state' => $product['state'],
			'id_product' => $product['id'],
			'status' => $offer['status'],
			'spec' => $prod_detail['spec'],
			'type' => $type['type']
		];
		response(200, "Offer", $res);
	}


	#[NoReturn] public function getAllOffer(){
		response(200, "Under Construction");
		//TODO all offer for user or admin
	}
	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(!checkToken($args['uri_args'][0], 4)){
			response(403, "Forbidden");
		}
		$token = $this->tom->selectByToken($args['uri_args'][0]);
		if($args['additional'][0] === 'id'){
			$args['additional'] = $token;
			$this->getOfferById($args);
		}elseif($args['additional'][0] === 'search'){
			$this->getAllOffer();
		}
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function post(array $args){
		$undefined = false;
		if(!checkToken($args['uri_args'][0], 4)){
			response(403, "Forbidden");
		}
		$tokm = new TokenModel();
		$user = $tokm->selectByToken($args['uri_args'][0]);
		if($user === false){
			response(500, "Internal Server Error");
		}
		if(!isset($args['post_args']['cat'], $args['post_args']['type'], $args['post_args']['brand'], $args['post_args']['model'], $args['post_args']['state'])){
			response(400, "Bad Request");
		}
		if($args['post_args']['cat'] !== "undefined"){
			if(!is_numeric($args['post_args']['cat']) || $this->cm->select($args['post_args']['cat']) === false){
				response(404, "Category Not Found");
			}
		}else{
			$undefined = true;
		}
		if($args['post_args']['type'] !== "undefined"){
			if(!is_numeric($args['post_args']['type'])){
				response(400, "Bad Request");
			}
			$type = $this->tm->select($args['post_args']['type']);
			if($type === false){
				response(404, "Type Not Found");
			}
			if($args['post_args']['cat'] !== "undefined" && (int)$args['post_args']['cat'] !== (int)$type['category']){
				response(400, "Mismatch in type and category");
			}
		}else{
			$undefined = true;
		}
		if($args['post_args']['brand'] !== "undefined"){
			if($this->sm->selectBrand($args['post_args']['brand']) === false){
				response(404, "Brand Not Found");
			}
		}else{
			$undefined = true;
		}
		if($args['post_args']['model'] !== "undefined"){
			if($this->sm->selectModel($args['post_args']['model']) === false){
				response(404, "Model Not Found");
			}
		}else{
			$undefined = true;
		}
		$state = $this->stm->select($args['post_args']['state']);
		if(!is_numeric($args['post_args']['state']) || $state === false){
			response(400, "Bad Request");
		}
		if(!$undefined){
			if(!isset($args['post_args']['spec']) || !is_array($args['post_args']['spec'])){
				response(400, "Bad Request");
			}
			$ref = $this->rm->selectByTypeBrandModel($args['post_args']['type'], $args['post_args']['brand'], $args['post_args']['model']);
			if($ref === false){
				response(500, "Internal Server Error");
			}
			if(empty($ref)){
				response(404, "Reference Not Found");
			}
			$spec = [];
			$bonus = 0.;
			foreach($ref['spec'] as $name => $value){
				if(!isset($args['post_args']['spec'][$name])){
					response(400, "Missing spec");
				}
				if(is_array($value)){
					if(!in_array($args['post_args']['spec'][$name], $value)){
						response(400, "Bad Value for Spec $name");
					}
					$spec_id = $this->sm->selectIdentical(['name' => $name, "value" => $args['post_args']['spec'][$name]]);
					if($spec_id === false){
						response(500, "Internal Server Error");
					}
					$spec_value = $this->rhsm->selectBySpecRef($spec_id['id'], $ref['id']);
					if($spec_value === false){
						response(500, "Internal Server Error");
					}
					$bonus += (double)$spec_value['value'];
					$spec[$name] = $spec_value['id'];
				}else{
					if($args['post_args']['spec'][$name] !== $value){
						response(400, "Bad Value for Spec $name");
					}
				}
			}
			$offer = ((double)$ref['buying_price'] + $bonus) * (100 - $state['penality']) / 100;
			$offer_id = $this->om->insert(['offer' => $offer, 'status' => 1, 'user' => $user['user']]);
			if($offer_id === false){
				response(500, "Internal Server Error");
			}
			$product_id = $this->pm->insert(['status' => 0, "state" => $args['post_args']['state'], 'ref' => $ref['id'], 'offer' => $offer_id]);
			if($product_id === false){
				response(500, "Internal Server Error");
			}
			foreach($spec as $s){
				if($this->psm->insert(['product' => $product_id, 'spec' => $s]) === false){
					response(500, "Internal Server Error");
				}
			}
			API_log($args['uri_args'][0], "OFFER-PRODUCT-PRODUCT_HAVE_SPEC", "created offer, product and specification linked");
			response(201, "Created", ['offer' => $offer_id]);
		}else{
			$offer_id = $this->om->insert(['offer' => 0, 'status' => 1, 'user' => $user['user']]);
			if($offer_id === false){
				response(500, "Internal Server Error");
			}
			$product_id = $this->pm->insert(['status' => 0, "state" => $args['post_args']['state'], 'ref' => 0, 'offer' => $offer_id]);
			if($product_id === false){
				response(500, "Internal Server Error");
			}
			API_log($args['uri_args'][0], "OFFER-PRODUCT-PRODUCT_HAVE_SPEC", "created offer, product and specification linked");
			response(201, "Created", ['offer' => $offer_id]);
		}
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