<?php


namespace Hyperion\API;
require_once "autoload.php";

class StoreController extends Controller
{

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		$pm = new ProductModel();
		if(count($args['uri_args']) === 0){
			$products = $pm->selectAll();
			if($products)
				response(200, "Product 1 to 500", $products);
			else
				response(204, "No product found");
		}elseif(count($args['uri_args']) === 1){
		    if(is_numeric($args['uri_args'][0])){
                $products = $pm->selectAll((int)$args['uri_args'][0]);
                if($products) {
                    $start = (int)$args['uri_args'][0] * 500 + 1;
                    $end = ((int)$args['uri_args'][0] + 1 )* 500;
                    response(200, "Product ${start} to ${end}", $products);
                }else
                    response(204, "No product found");
            }
        }
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args){return false;}

	/**
	 * @inheritDoc
	 */
	public function put(array $args){return false;}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){return false;}
}