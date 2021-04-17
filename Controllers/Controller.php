<?php


namespace Hyperion\API;


/**
 * Class Controller
 * @package Controller
 * @author Macaron
 */
interface Controller
{
	/**
	 * Must be instanced for using the get() method and control GET request
	 * @param array $args Argument passed to the controller by the router
	 * @return mixed
	 */
	public function get(array $args);
	/**
	 * Must be instanced for using the post() method and control POST request
	 * @param array $args Argument passed to the controller by the router
	 * @return mixed
	 */
	public function post(array $args);
	/**
	 * Must be instanced for using the put() method and control PUT request
	 * @param array $args Argument passed to the controller by the router
	 * @return mixed
	 */
	public function put(array $args);
	/**
	 * Must be instanced for using the delete() method and control DELETE request
	 * @param array $args Argument passed to the controller by the router
	 * @return mixed
	 */
	public function delete(array $args);
}