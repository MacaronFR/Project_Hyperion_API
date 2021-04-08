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
		}
	}

	/**
	 * @inheritDoc
	 */
	public function post(array $args)
	{
		// TODO: Implement post() method.
	}

	/**
	 * @inheritDoc
	 */
	public function put(array $args)
	{
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args)
	{
		// TODO: Implement delete() method.
	}
}