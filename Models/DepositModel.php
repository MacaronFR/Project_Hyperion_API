<?php


namespace Hyperion\API;


class DepositModel extends Model{

    protected string $id_name = "id_deposit";
    protected string $table_name = "DEPOSIT";
    protected array $column = [
        "deposit"=>"id_deposit",
        "space"=>"space",
        "address"=>"address",
    ];
}