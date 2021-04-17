<?php


namespace Hyperion\API;


class ProductInCartModel extends Model{
    protected string $id_name = "id_product_in_cart";
    protected string $table_name = "PRODUCT_IN_CART";
    protected array $column = [
     "prod_cart"=>"id_prod_in_cart",
     "product"=>"id_product",
     "cart"=>"id_cart"
    ];

}