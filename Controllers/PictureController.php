<?php


namespace Hyperion\API;


use JetBrains\PhpStorm\NoReturn;

class PictureController implements Controller{

	private ProductPicturesModel $ppm;
	private FilesModel $fm;

	public function __construct(){
		$this->ppm = new ProductPicturesModel();
		$this->fm = new FilesModel();
	}

	/**
	 * @inheritDoc
	 */
	public function get(array $args){
		if(!is_numeric($args['uri_args'][0])){
			response(400, "Bad Request");
		}
		if(isset($args['uri_args'][1])){
			if(!is_numeric($args['uri_args'][1])){
				response(400, "Bad Request");
			}
			$limit = $args['uri_args'][1];
		}else{
			$limit = 3;
		}
		$pictures = $this->ppm->selectAllByProduct($args['uri_args'][0]);
		if($pictures === false){
			response(500, "Internal Server Error");
		}
		if(empty($pictures)){
			response(204, "No content");
		}
		$files = [];
		$i = 0;
		foreach($pictures as $p){
			if($i < $limit){
				++$i;
			}else{
				break;
			}
			$file = $this->fm->selectWithB64($p['file']);
			if($file === false){
				response(500, "Internal Server Error");
			}
			unset($file['file_path'], $file['creator'], $file['id']);
			$files[] = $file;
		}
		response(200, "Files", $files);
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
		// TODO: Implement put() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete(array $args){
		// TODO: Implement delete() method.
	}
}