<?php


namespace Hyperion\API;
require_once "autoload.php";

class StoreController extends Controller
{

	/**
     * Retrieve the first 500 products on /store or the Nth 500 products for /store/N
	 * @inheritDoc
	 */
	public function get(array $args){
		$pm = new ProductModel();
		if(count($args['uri_args']) === 0){
			$iteration = 0;
		}elseif(count($args['uri_args']) === 1){
		    if(is_numeric($args['uri_args'][0])){
                $iteration = (int)$args['uri_args'][0];
            }else{
		        response(400, "Bad Request");
		        return;
            }
        }else{
            response(400, "Bad Request");
            return;
        }
        $products = $pm->selectAll($iteration);
        if($products) {
            $start = (int)$args['uri_args'][0] * 500 + 1;
            $end = ((int)$args['uri_args'][0] + 1 )* 500;
            response(200, "Product $start to $end", $products);
        }else
            response(204, "No product found");
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