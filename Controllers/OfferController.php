<?php


namespace Hyperion\API;

require_once "autoload.php";

use DateTime;
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
	private FilesModel $fm;
	private ProductPicturesModel $ppm;
	private array $auth_type = ['image/png' => 'png', "image/jpeg" => 'jpg'];

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
		$this->fm = new  FilesModel();
		$this->ppm = new ProductPicturesModel();
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
			response(501, "Internal Server Error");
		}
		$prod_detail = $this->pm->selectWithDetail($product['id']);
		if($prod_detail === false){
			response(502, "Internal Server Error");
		}
		$reference = $this->rm->select($product['ref']);
		if($reference === false){
			response(503, "Internal Server Error");
		}
		$type = $this->tm->select($reference['type']);
		if($type === false){
			response(504, "Internal Server Error");
		}
		$pictures_id = $this->ppm->selectAllByProduct($product['id']);
		if($pictures_id === false){
			response(505, "Internal Server Error");
		}
		if(!empty($pictures_id)){
			foreach($pictures_id as $pid){
				$f = $this->fm->selectWithB64($pid['file']);
				if($f === false){
					response(506, "Internal Server Error");
				}
				unset($f['file_path'], $f['creator'], $f['id']);
				$files[] = $f;
			}
		}
		$res = [
			'id_offer' => $offer['id'],
			'category' => $type['category'],
			'offer' => $offer['offer'],
			'counter_offer' => $offer['counter_offer'],
			'state' => $product['state'],
			'id_product' => $product['id'],
			'status' => $offer['status'],
			'spec' => $prod_detail['spec'],
			'type' => $type['type'],
			'type_id' => $type['id'],
			'files' => $files ?? []
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

		$user_token = $this->tom->selectByToken($args['uri_args'][0]);
		if($user_token === false){
			response(500, "Internal Server Error");
		}
		if(!isset($args['post_args']['cat'], $args['post_args']['type'], $args['post_args']['brand'], $args['post_args']['model'], $args['post_args']['state'], $args['post_args']['files']) || count($args['post_args']['files']) > 3){
			response(400, "Bad Request");
		}
		foreach($args['post_args']['files'] as &$f){
			if(!isset($f['content'], $f['type'], $f['filename']) || !in_array($f['type'], array_keys($this->auth_type))){
				response(400, "Bad Request");
			}
			$f['filename'] = replace_file_ext($f['filename'], $this->auth_type[$f['type']]);
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
				response(501, "Internal Server Error");
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
				if(count($value) !== 1){
					if(!$this->foundInSpecList($value, $args['post_args']['spec'][$name])){
						response(400, "Bad Value for Spec $name");
					}
					$spec_id = $this->sm->selectIdentical(['name' => $name, "value" => $args['post_args']['spec'][$name]]);
					if($spec_id === false){
						response(502, "Internal Server Error");
					}
					$spec_value = $this->rhsm->selectBySpecRef($spec_id['id'], $ref['id']);
					if($spec_value === false){
						response(503, "Internal Server Error");
					}
					$bonus += (double)$spec_value['value'];
					$spec[$name] = $spec_id['id'];
				}else{
					if($args['post_args']['spec'][$name] !== $value[0][0]){
						response(400, "Bad Value for Spec $name");
					}
				}
			}
			$offer = ((double)$ref['buying_price'] + $bonus) * (100 - $state['penality']) / 100;
			$offer_id = $this->om->insert(['offer' => $offer, 'status' => 1, 'user' => $user_token['user']]);
			if($offer_id === false){
				response(504, "Internal Server Error");
			}
			$product_id = $this->pm->insert(['status' => 0, "state" => $args['post_args']['state'], 'ref' => $ref['id'], 'offer' => $offer_id]);
			if($product_id === false){
				response(505, "Internal Server Error");
			}
			foreach($spec as $s){
				if($this->psm->insert(['product' => $product_id, 'spec' => $s]) === false){
					response(506, "Internal Server Error");
				}
			}
		}else{
			$offer_id = $this->om->insert(['offer' => 0, 'status' => 1, 'user' => $user_token['user']]);
			if($offer_id === false){
				response(507, "Internal Server Error");
			}
			$product_id = $this->pm->insert(['status' => 0, "state" => $args['post_args']['state'], 'ref' => 0, 'offer' => $offer_id]);
			if($product_id === false){
				response(508, "Internal Server Error");
			}
		}
		foreach($args['post_args']['files'] as $file){
			$save_name = md5(time() . $file['filename']) . ".b64";
			if(file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/images/offer/" . $save_name, $file['content']) === false){
				response(509, "Internal Server Error");
			}
			$file_id = $this->fm->insert(['file_path' => "images/offer/" . $save_name, "file_name" => $file['filename'], 'type' => $file['type'], 'creator' => $user_token['user']]);
			if($file_id === false){
				response(510, "Internal Server Error");
			}
			if($this->ppm->insert(['file' => $file_id, 'product' => $product_id]) === false){
				response(511, "Internal Server Error");
			}
		}
		API_log($args['uri_args'][0], "OFFER-PRODUCT-PRODUCT_HAVE_SPEC", "created offer, product and specification linked");
		response(201, "Created", ['offer' => $offer_id]);
	}

	private function foundInSpecList($array, $value): bool{
		for($i = 0; $i < count($array); ++$i){
			if($array[$i][0] === $value){
				return true;
			}
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	#[NoReturn] public function put(array $args){
		if($args['additional'][0] === "counter"){
			$this->counterOffer($args);
		}elseif($args['additional'][0] === "send"){
			$this->sendCounter($args);
		}elseif($args['additional'][0] === "set"){
			$this->setOffer($args);
		}
	}

	#[NoReturn] private function counterOffer(array $args){
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$token = $this->tom->selectByToken($args['uri_args'][0]);
		if($token === false){
			response(403, "Forbidden");
		}
		$offer = $this->om->select($args['uri_args'][1]);
		if($offer === false){
			response(404, "Offer Not Found");
		}
		if($token['user'] !== $offer['user']){
			response(401, "Unauthorized");
		}
		if((int)$offer['status'] !== 4){
			response(400, "Bad Request");
		}
		$val = match ($args['additional'][1]) {
			"accept" => 6,
			"refuse" => 5
		};
		if(!$this->om->update($offer['id'], ['status' => $val])){
			response(500, "Internal Server Error");
		}
		if($val === 6){
			$prod = $this->pm->select($offer['id'], "offer");
			if($prod === false){
				response(501, "Internal Server Error");
			}
			if($this->pm->update($prod['id'], ['status' => 2, 'buy_d' => (new DateTime())->format("Y-m-d H:i:s"), "buy_p" => $offer['counter_offer'], "sell_p" => ((double)$offer['counter_offer'] * 1.3)])){
				response(200, "Offer Updated");
			}
			response(502, "Internal Server Error");
		}
		response(200, "Offer Updated");
	}

	private function putRetrieveData(array $args): array{
		if(!is_numeric($args['uri_args'][1])){
			response(400, "Bad Request");
		}
		$token = $this->tom->selectByToken($args['uri_args'][0]);
		if($token === false || $token['scope'] >= 3){
			response(401, "Unauthorized");
		}
		$offer = $this->om->select($args['uri_args'][1]);
		if($offer === false){
			response(404, "Offer Not Found");
		}
		if((int)$offer['status'] !== 3){
			response(404, "Offer Not Found");
		}
		if($offer['expert'] !== $token['user']){
			response(403, "Forbidden");
		}
		$product = $this->pm->select($offer['id'], "offer");
		if($product === false){
			response(500, "Internal Server Error");
		}
		$spec = $this->pm->selectWithDetail($product['id']);
		if($spec === false){
			response(501, "Internal Server Error");
		}
		$spec = $spec['spec'];
		$ref = $this->rm->select($product['ref']);
		if($ref === false){
			response(502, "Internal Server Error");
		}
		return ['spec' => $spec, 'product' => $product, 'offer' => $offer, 'ref' => $ref];
	}

	#[NoReturn] private function sendCounter(array $args){
		$data = $this->putRetrieveData($args);
		$bonus = 0.;
		foreach($data['spec'] as $n => $v){
			if($n !== "brand" && $n !== "model"){
				$spec_id = $this->sm->selectIdentical(['name' => $n, 'value' => $v]);
				if($spec_id === false){
					response(503, "Internal Server Error");
				}
				$rhs = $this->rhsm->selectBySpecRef($spec_id['id'], $data['ref']['id']);
				if($rhs === false){
					response(504, "Internal Server Error");
				}
				$bonus += (double)$rhs['value'];
			}
		}
		$state = $this->stm->select($data['product']['state']);
		if($state === false){
			response(505, "Internal Server Error");
		}
		$price = ($data['ref']['buying'] + $bonus) * (100 - $state['penality']) / 100;
		if($this->om->update($data['offer']['id'], ['status' => 4, 'counter_offer' => $price])){
			response(200, "Counter Offer Send");
		}
		response(506, "Internal Server Error");
	}

	private function setOffer(array $args){
		$data = $this->putRetrieveData($args);
		$old_val = [
			'type' => (int)$data['ref']['type'],
			'brand' => $data['spec']['brand'],
			'model' => $data['spec']['model'],
			'state' => (int)$data['product']['state'],
			'spec' => $data['spec']
		];
		unset($old_val['spec']['brand'], $old_val['spec']['model']);
		$new_val = array_intersect_key($args['put_args'], $old_val);
		if(isset($new_val['spec'])){
			$new_val['spec'] = array_intersect_key($new_val['spec'], $old_val['spec']);
		}
		if(isset($new_val['type'])){
			$type = $this->tm->select($new_val['type']);
			if($type === false){
				response(404, "Type not Found");
			}
		}
		if(isset($new_val['brand'])){
			$brand = $this->sm->selectIdentical(['name' => 'brand', 'value' => $new_val['brand']]);
			if($brand === false){
				response(404, "Brand not Found");
			}
		}
		if(isset($new_val['model'])){
			$model = $this->sm->selectIdentical(['name' => 'model', 'value' => $new_val['model']]);
			if($model === false){
				response(404, "Model not Found");
			}
		}
		if(isset($new_val['state'])){
			if((int)$new_val['state'] < 1 || (int)$new_val['state'] > 5){
				response(400, "Bad Request");
			}
		}
		$new_ref = $this->rm->selectByTypeBrandModel(
			$new_val['type'] ?? $old_val['type'],
			$new_val['brand'] ?? $old_val['brand'],
			$new_val['model'] ?? $old_val['model']
		);
		if($new_ref === false){
			response(500, "Internal Server Error");
		}
		if(empty($new_ref)){
			response(404, "Product Not Found");
		}
		if(isset($new_val['spec'])){
			foreach($new_val['spec'] as $n => $v){
				if(!isset($new_ref['spec'][$n])){
					response(400, "Bad Request");
				}
				if(!$this->foundInSpecList($new_ref['spec'][$n], $v)){
					response(400, "Bad Request");
				}
				$spec_id = $this->sm->selectIdentical(['name' => $n, 'value' => $v]);
				if($spec_id === false){
					response(501, "Internal Server Error");
				}
				$new_val['spec'][$n] = $spec_id['id'];
			}
		}
		if($data['product']['ref'] !== $new_ref['id']){
			if(!$this->pm->update($data['product']['id'], ['ref' => $new_ref['id']])){
				response(502, "Internal Server Error");
			}
			$old_spec = $this->psm->selectAllByProduct($data['product']['id']);
			if($old_spec === false){
				response(503, "Internal Server Error");
			}
			foreach($new_ref['spec'] as $n => $a){
				if(count($a) > 1){
					if(!isset($new_val['spec'][$n])){
						response(400, "Bad Request");
					}
					if(!$this->psm->insert(['product' => $data['product']['id'], 'spec' => $new_val['spec'][$n]])){
						response(504, "Internal Server Error");
					}
				}
			}
			foreach($old_spec as $o){
				if(!$this->psm->delete($o['id'])){
					response(505, "Internal Server Error");
				}
			}
		}else{
			if(isset($new_val['spec'])){
				foreach($new_val['spec'] as $n => $v){
					if($old_val['spec'][$n] !== $v){
						$old_spec = $this->sm->selectIdentical(['name' => $n, 'value' => $old_val['spec'][$n]]);
						if($old_spec === false){
							response(506, "Internal Server Error");
						}
						$psm_id = $this->psm->selectBySpecProd($old_spec['id'], $data['product']['id']);
						if($psm_id !== false){
							if(!$this->psm->delete($psm_id['id'])){
								response(508, "Internal Server Error");
							}
							if(!$this->psm->insert(['product' => $data['product']['id'], 'spec' => $v])){
								response(509, "Internal Server Error");
							}
						}
					}
				}
			}
		}
		response(200, "Offer Updated");
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}