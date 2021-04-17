<?php


namespace Hyperion\API;
require_once "autoload.php";


// get all types
class TypeController implements Controller{
		public function get(array $args){
				if(count($args['uri_args']) === 2){
						$iteration = (int)$args['uri_args'][1];
				}else{
						$iteration = 0;
				}
				if(isset($args['uri_args'][0]) && is_numeric($args['uri_args'][0])){
						$tm = new TypeModel();
						$types = $tm->selectByCategory((int)$args['uri_args'][0], $iteration);
						if($types !== false){
								response(200, "type from category ${args['uri_args'][0]}");
						}else{
								response(404, 'Not Found Bitch');
						}
				}

		}

		public function post(array $args){
				// TODO: Implement post() method.
		}

		public function put(array $args){
				// TODO: Implement put() method.
		}

		public function delete(array $args){
				// TODO: Implement delete() method.
		}

}
