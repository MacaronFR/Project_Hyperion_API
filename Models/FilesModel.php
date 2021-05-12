<?php


namespace Hyperion\API;

require_once "autoload.php";

class FilesModel extends Model{
	protected string $id_name = "id_file";
	protected string $table_name = "FILES";
	protected array $column = [
		"file_name" => "file_name",
		"file_path" => "file_path",
		"type" => "type",
		"creator" => "creator"
	];

	public function selectWithB64(mixed $value, string $column = ""){
		$file = $this->select($value, $column);
		if($file === false){
			return false;
		}
		$file['content'] = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $file['file_path']);
		return $file;
	}
}