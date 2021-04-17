<?php


namespace Hyperion\API;


class FilesModel extends Model{
    protected string $id_name = "id_files";
    protected string $table_name = "FILES";
    protected array $column = [
        "file"=>"id_file",
        "name"=>"name",
        "directory"=>"dir",
        "type"=>"type",
        "creator"=>"creator"
    ];
}