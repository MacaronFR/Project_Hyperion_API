<?php


namespace Hyperion\API;

require_once "autoload.php";

class FilesModel extends Model{
	protected string $id_name = "id_files";
	protected string $table_name = "FILES";
	protected array $column = [
		"name" => "name",
		"directory" => "dir",
		"type" => "type",
		"creator" => "creator"
	];
}