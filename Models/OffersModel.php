<?php


namespace Hyperion\API;


class offersModel extends Model{
    protected string $id_name = "id_offers";
    protected string $table_name = "OFFERS";
    protected array $column = [
        "offer"=>"id_offer",
        "creation"=>"id_creation",
        "offer"=>"offer",
        "counter"=>"counter_offer",
        "status"=>"status",
        "user"=>"id_user"
    ];

}