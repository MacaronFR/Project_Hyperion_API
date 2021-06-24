<?php

use Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use Hyperion\API\Router;
use Hyperion\API\{ProductHierarchyController,ReferenceHierarchyController,BrandModelController,SpecController};
use Hyperion\API\{CartController,
	ContributeController,
	ExpertOfferController,
	InvoiceController,
	OfferController,
	PendingOfferController,
	ProjectController,
	ReceptionOfferController,
	ShopProductController,
	TypeController,
	TerminatedOfferController,
	ShopController,
	PictureController,
	StateController};

require_once "autoload.php";

if($_SERVER['REQUEST_METHOD'] === "OPTIONS"){
	response(200, "OK");
}

$rt = new Router();
// /token/{client_id}/{client_secret}[/{user_mail}/{user_passwd}]
$rt->get("/token/*/*{/*/*}", OAuthController::class);
// /connect/{client_id}/{client_secret}/{user_mail}/{user_passwd}
$rt->get("/connect/*/*/*/*", ConnectionController::class);
// /disconnect/{user_token}
$rt->delete("/disconnect/*", ConnectionController::class);
// /inscription/{client_token}
// {}
$rt->post("/inscription/*", ConnectionController::class);
//Store
// /store[/{page}]
$rt->get("/store{/*}", StoreController::class);
//Profile
// /me/{user_token}
$rt->get("/me/*", ProfileController::class);
$rt->put("/me/*", ProfileController::class, ['me']);
$rt->delete("/me/*",ProfileController::class);

$rt->get("/users/*{/*{/search/*{/order/*/sort/*}}}", ProfileController::class, ['users']);
// /profile/{token}/{user_id}
$rt->get("/profile/*/*",ProfileController::class);
$rt->put("/profile/*/*",ProfileController::class);
$rt->delete("/profile/*/*",ProfileController::class);
// /category[/{page}]
$rt->get("/category{/*{/search/*{/order/*/sort/*}}}", CategoryController::class);
//$rt->get("/category/*", CategoryController::class);
// /category/{user_token}
// {"name": <category_name>}
$rt->post("/category/*", CategoryController::class);
// /category/{client_token}/{id_cat}
// {"name": <new_category_name>}
$rt->put("/category/*/*", CategoryController::class);
// /category/{client_token}/{id_cat}
$rt->delete("/category/*/*", CategoryController::class);
//Type
$rt->get("/type{/*{/search/*{/order/*/sort/*}}}", TypeController::class);
$rt->get("/typeCat{/*{/search/*{/order/*/sort/*}}}", TypeController::class, ['cat']);
// /type/{user_token}/{id_type}
$rt->put("/type/*/*", TypeController::class);
// /type/{user_token}/{id_type}
$rt->delete("/type/*/*", TypeController::class);
// /type/{user_token}
$rt->post("/type/*", TypeController::class);
// /category/type/{id_category}[/{page}]
$rt->get("/category/*/type{/*}",BrandModelController::class, ["type"]);
// /category/brand/{id_category}[/{page}]
$rt->get("/category/*/brand{/*}",BrandModelController::class, ["brandcat"]);
// /type/{id_type}/product[/{page}]
$rt->get("/type/*/product{/*}", ProductHierarchyController::class, ["type_product"]);
// /type/{id_type}/reference[/{page}]
$rt->get("/type/*/reference{/*}", ReferenceHierarchyController::class, ["type_reference"]);
// /brand/{brand_name}/product[/{page}]
$rt->get("/brand/*/product{/*}", ProductHierarchyController::class, ["brand_product"]);
// /brand/{brand_name}/reference[/{page}]
$rt->get("/brand/*/reference{/*}", ReferenceHierarchyController::class, ["brand_reference"]);
// /type/{id_type}/brand/{brand_name}/product[/{page}]
$rt->get("/type/*/brand/*/product{/*}", ProductHierarchyController::class, ["type_brand_product"]);
// /type/{id_type}/brand/{brand_name}/reference[/{page}]
$rt->get("/type/*/brand/*/reference{/*}", ReferenceHierarchyController::class, ["type_brand_reference"]);
// /type/{id_type}/brand[/{page}]
$rt->get("/type/*/brand{/*}", BrandModelController::class, ["type_brand"]);
// /type/{id_type}/brand[/{page}]
$rt->get("/type/*/model{/*}", BrandModelController::class, ["type_model"]);
// /brand/{brand_name}/model[/{page}]
$rt->get("/brand/*/model{/*}", BrandModelController::class, ["brand_model"]);
// /type/{id_type}/brand/{brand_name}/model[/{page}]
$rt->get("/type/*/brand/*/model{/*}", BrandModelController::class, ["type_brand_model"]);
// /brand[/{page}]
$rt->get("/brand{/*}", BrandModelController::class, ["brand"]);
// /model[/{page}]
$rt->get("/model{/*}", BrandModelController::class, ["model"]);
// /model/{model_name}/product[/{page}]
$rt->get("/model/*/product{/*}", ProductHierarchyController::class, ['model_product']);
// /brand/{brand_name}/model/{model_name}/product[/{page}]
$rt->get("/brand/*/model/*/product{/*}", ProductHierarchyController::class, ['brand_model_product']);
// /type/{type_id}/model/{model_name}/product/[/{page}]
$rt->get("/type/*/model/*/product{/*}", ProductHierarchyController::class, ['type_model_product']);
// /type/{type_id}/brand/{brand_name}/model/{model_name}/product[/{page}]
$rt->get("/type/*/brand/*/model/*/product{/*}", ProductHierarchyController::class, ['type_brand_model_product']);
// /model/{model_name}/reference
$rt->get("/model/*/reference", ReferenceHierarchyController::class, ['model_reference']);
// /brand/{brand_name}/model/{model_name}/reference
$rt->get("/brand/*/model/*/reference", ReferenceHierarchyController::class, ['brand_model_reference']);
// /type/{type_id}/model/{model_name}/reference
$rt->get("/type/*/model/*/reference", ReferenceHierarchyController::class, ['type_model_reference']);
// /type/{type_id}/brand/{brand_name}/model/{model_name}/reference
$rt->get("/type/*/brand/*/model/*/reference", ReferenceHierarchyController::class, ['type_brand_model_reference']);

$rt->get("/specification/name",SpecController::class, ['name']);
$rt->get("/specification/type/*/brand/*", SpecController::class, ['type', 'brand']);
$rt->get("/specification/type/*", SpecController::class, ['type']);
$rt->get("/specification/brand/*", SpecController::class, ['brand']);
// /specification[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/specification{/*{/search/*{/order/*/sort/*}}}",SpecController::class, ['all']);
// /specification/{token}/{id}
$rt->put("/specification/*/*",SpecController::class);
// /specification/{token}/{id}
$rt->delete("/specification/*/*",SpecController::class);
// /specification/{token}
$rt->post('/specification/*',SpecController::class);
// /reference[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/reference{/*{/search/*{/order/*/sort/*}}}",ReferenceHierarchyController::class, ['ref']);
// /reference/detail[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/reference/detail{/*{/search/*{/order/*/sort/*}}}",ReferenceHierarchyController::class, ['ref_detail']);
// /reference/{token}
$rt->post('/reference/*', ReferenceHierarchyController::class);
// /reference/{token}/{id}
$rt->delete("/reference/*/*", ReferenceHierarchyController::class);
// /product[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/product{/*{/search/*{/order/*/sort/*}}}", ProductHierarchyController::class, ["prod"]);
$rt->get("/product_detail{/*{/search/*{/order/*/sort/*}}}", ProductHierarchyController::class, ["prod_detail"]);
// /product/{token}/{id}
$rt->delete("/product/*/*", ProductHierarchyController::class);
// OFFER
// /offer/{token}/{id} || /offer/{token}[/{page}[/search/*[/order/*/sort/*]]]
$rt->get("/offer/pending/all/*{/*}", PendingOfferController::class, ['all']);
$rt->get("/offer/pending/*/user/*{/*}", PendingOfferController::class, ['user']);
$rt->get("/offer/pending/*{/*}", PendingOfferController::class);
$rt->get("/offer/terminated/all/*{/*}", TerminatedOfferController::class, ['all']);
$rt->get("/offer/terminated/*/user/*{/*}", TerminatedOfferController::class, ['user']);
$rt->get("/offer/terminated/*{/*}", TerminatedOfferController::class);
$rt->get("/offer/*/*", OfferController::class, ['id']);
//$rt->get("/reference{/*{/search/*{/order/*/sort/*}}}",ReferenceHierarchyController::class, ['search']);
// /offer/{token}
$rt->post("/offer/*", OfferController::class);

$rt->put("/offer/counter/send/*/*", OfferController::class, ['send']);
$rt->put("/offer/counter/accept/*/*", OfferController::class, ['counter', 'accept']);
$rt->put("/offer/counter/refuse/*/*", OfferController::class, ['counter', 'refuse']);
$rt->put("/offer/set/*/*", OfferController::class, ['set']);

// PROJECT
$rt->get("/project/nologo/popular{/*}",ProjectController::class, ['popular', 'nologo']);
$rt->get("/project/nologo/latest{/*}",ProjectController::class, ['latest', 'nologo']);
$rt->get("/project/nologo{/*{/search/*{/order/*/sort/*}}}",ProjectController:: class, ['all', 'nologo']);

$rt->get("/project/logo/*", ProjectController::class, ['logo']);

$rt->get("/project/popular{/*}",ProjectController::class, ['popular']);
$rt->get("/project/latest{/*}",ProjectController::class, ['latest']);
$rt->get("/project{/*{/search/*{/order/*/sort/*}}}",ProjectController:: class, ['all']);
$rt->get("/project/invalid{/*{/search/*{/order/*/sort/*}}}",ProjectController:: class, ['invalid']);
$rt->post("/project/*",ProjectController::class);

// CONTRIBUTION
$rt->post("/project/contribute/*", ContributeController::class, ['contribute']);

//
$rt->get("/invoice/me/*",InvoiceController::class, ['me']);
$rt->get("/invoice/all/*",InvoiceController::class, ['all']);
$rt->get("/invoice/*/*", InvoiceController::class, ['one']);
$rt->get("/invoice/cart/*/*", InvoiceController::class, ['cart']);
$rt->put("/invoice/confirm/*", InvoiceController::class);

//Shop
$rt->get("/shop{/*}", ShopController::class, ['main']);
$rt->get("/shop/cat/*/type/*/brand/*/*{/filter/_}", ShopController::class, ['cat', 'type', 'brand']);
$rt->get("/shop/cat/*/brand/*/*{/filter/_}", ShopController::class, ['cat', 'brand']);
$rt->get("/shop/cat/*/type/*/*{/filter/_}", ShopController::class, ['cat', 'type']);
$rt->get("/shop/type/*/brand/*/*{/filter/_}", ShopController::class, ['type', 'brand']);
$rt->get("/shop/type/*/*{/filter/_}", ShopController::class, ['type']);
$rt->get("/shop/brand/*/*{/filter/_}", ShopController::class, ['brand']);
$rt->get("/shop/cat/*/*{/filter/_}", ShopController::class, ['cat']);
$rt->get("/shop/product/*", ShopProductController::class);

$rt->get("/product/picture/*{/*}", PictureController::class);

$rt->get("/state", StateController::class);

$rt->get("/expert/offer/reception/all/*{/*{/search/*{/order/*/sort/*}}}", ExpertOfferController::class, ['reception']);
$rt->get("/expert/offer/pending/*{/*{/search/*{/order/*/sort/*}}}", ExpertOfferController::class, ['pending']);
$rt->get("/expert/offer/history/*{/*{/search/*{/order/*/sort/*}}}", ExpertOfferController::class, ['history']);
$rt->get("/expert/offer/*{/*{/search/*{/order/*/sort/*}}}", ExpertOfferController::class);
$rt->post("/expert/offer/*/*",ExpertOfferController::class);
$rt->put("/expert/offer/counter_offer/*",ExpertOfferController::class, ['counter']);
$rt->put("/expert/offer/reception/*/*", ExpertOfferController::class, ['reception']);

$rt->post("/cart/*", CartController::class);
$rt->post("/cart/product/*", CartController::class, ['add_prod']);
$rt->get("/cart/product/*", CartController::class, ['product']);
$rt->get("/cart/active/*", CartController::class, ['active']);
$rt->get("/cart/*/*", CartController::class);
$rt->put("/cart/command/*", CartController::class);
$rt->delete("/cart/*", CartController::class);
$rt->delete("/cart/product/*/*", CartController::class, ['prod']);

if(!$rt->getRouted()){
	response(404, "Not Found");
}