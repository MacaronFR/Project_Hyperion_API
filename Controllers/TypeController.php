<?php


namespace Hyperion\API;


class TypeController implements Controller{
		private TypeModel $tym;
		public function __construct(){
				$this->tym = new TypeModel();
		}

		/**
		 * @inheritDoc
		 */
		public function get(array $args){
				$tmp = 0;
				if(count($args['uri_args'] === 1) && is_numeric($args['uri_args'][0])){
						$counter = (int)$args['uri_args'][0];
				}else{
						response(400,"Bad Request");
				}
				$res = $this->tym->selectAll($tmp);
				$start = $tmp * 500 + 1;
				$end = ($tmp + 1) * 500;
				if($res !== false){if(empty($res)){ response(204,"What's Wrong with you");}else{
						response(200,"Type $start to $end",$res);
				}}else{
						response(500,"Big Big Big LOL");
				}

		}

		/**
		 * @inheritDoc
		 */
		public function post(array $args){
				if(checkToken($args['uri_args'][0],3)) if(isset($args['post_args']['name']) && count($args['post_args']) ===1){
						if($this->tym->selectByName($args['post_args']['name']) === false){
								if($this->tym->insert(['name' => $args['post_args']['name']])){
										response(201, "Type has been created");
								}else{
									response(402, "Type Already Exist");
								}
						}else{
								response(400,"Bad Requests");
						}
				}else{
					response(403,"Forbidden");
				}
		}

		public function put(array $args){
				// TODO: Implement put() method.
		}

		public function delete(array $args){
				// TODO: Implement delete() method.
		}
}