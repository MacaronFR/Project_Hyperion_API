<?php


namespace Hyperion\API;


class PackagesModel extends Model{
    protected string $id_name = "id_package";
    protected string $table_name = "PACKAGES";
    protected array $column = [
        "package"=>"id_package",
        "number"=>"number",
        "offer"=>"id_offer",
        "address"=>"id_address"
    ];
}