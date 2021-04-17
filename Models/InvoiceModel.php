<?php


namespace Hyperion\API;


class InvoiceModel extends Model{
    protected string $id_name = "id_invoice";
    protected string $table_name = "INVOICE";
    protected array $column = [
        "invoice"=>"id_invoice",
        "creation"=>"date_creation",
        "total"=>"total",
        "file"=>"id_file",
        "cart"=>"id_cart",
        "offer"=>"id_offer"
    ];
}