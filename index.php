<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use \Hyperion\API\Router;
use \Hyperion\API\{ProductHierarchyController,ReferenceHierarchyController,MarkModelController,SpecController};
use \Hyperion\API\{TypeController};

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
// /profile/{token}/{user_id}
$rt->get("/profile/*/*",ProfileController::class);
$rt->put("/profile/*/*",ProfileController::class);
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
$rt->get("/type_cat{/*{/search/*{/order/*/sort/*}}}", TypeController::class, ['cat']);
// /type/{user_token}/{id_type}
$rt->put("/type/*/*", TypeController::class);
// /type/{user_token}/{id_type}
$rt->delete("/type/*/*", TypeController::class);
// /type/{user_token}
$rt->post("/type/*", TypeController::class);
// /category/type/{id_category}[/{page}]
$rt->get("/category/*/type{/*}",MarkModelController::class, ["type"]);
// /type/{id_type}/product[/{page}]
$rt->get("/type/*/product{/*}", ProductHierarchyController::class, ["type_product"]);
// /type/{id_type}/reference[/{page}]
$rt->get("/type/*/reference{/*}", ReferenceHierarchyController::class, ["type_reference"]);
// /mark/{mark_name}/product[/{page}]
$rt->get("/mark/*/product{/*}", ProductHierarchyController::class, ["mark_product"]);
// /mark/{mark_name}/reference[/{page}]
$rt->get("/mark/*/reference{/*}", ReferenceHierarchyController::class, ["mark_reference"]);
// /type/{id_type}/mark/{mark_name}/product[/{page}]
$rt->get("/type/*/mark/*/product{/*}", ProductHierarchyController::class, ["type_mark_product"]);
// /type/{id_type}/mark/{mark_name}/reference[/{page}]
$rt->get("/type/*/mark/*/reference{/*}", ReferenceHierarchyController::class, ["type_mark_reference"]);
// /type/{id_type}/mark[/{page}]
$rt->get("/type/*/mark{/*}", MarkModelController::class, ["type_mark"]);
// /mark/{mark_name}/model[/{page}]
$rt->get("/mark/*/model{/*}", MarkModelController::class, ["mark_model"]);
// /type/{id_type}/mark/{mark_name}/model[/{page}]
$rt->get("/type/*/mark/*/model{/*}", MarkModelController::class, ["type_mark_model"]);
// /mark[/{page}]
$rt->get("/mark{/*}", MarkModelController::class, ["mark"]);
// /model[/{page}]
$rt->get("/model{/*}", MarkModelController::class, ["model"]);
// /model/{model_name}/product[/{page}]
$rt->get("/model/*/product{/*}", ProductHierarchyController::class, ['model_product']);
// /mark/{mark_name}/model/{model_name}/product[/{page}]
$rt->get("/mark/*/model/*/product{/*}", ProductHierarchyController::class, ['mark_model_product']);
// /type/{type_id}/model/{model_name}/product/[/{page}]
$rt->get("/type/*/model/*/product{/*}", ProductHierarchyController::class, ['type_model_product']);
// /type/{type_id}/mark/{mark_name}/model/{model_name}/product[/{page}]
$rt->get("/type/*/mark/*/model/*/product{/*}", ProductHierarchyController::class, ['type_mark_model_product']);
// /model/{model_name}/reference
$rt->get("/model/*/reference", ReferenceHierarchyController::class, ['model_reference']);
// /mark/{mark_name}/model/{model_name}/reference
$rt->get("/mark/*/model/*/reference", ReferenceHierarchyController::class, ['mark_model_reference']);
// /type/{type_id}/model/{model_name}/reference
$rt->get("/type/*/model/*/reference", ReferenceHierarchyController::class, ['type_model_reference']);
// /type/{type_id}/mark/{mark_name}/model/{model_name}/reference
$rt->get("/type/*/mark/*/model/*/reference", ReferenceHierarchyController::class, ['type_mark_model_reference']);
// /specification[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/specification{/*{/search/*{/order/*/sort/*}}}",SpecController::class);
$rt->get("/specification/name{/*}",SpecController::class, ['name']);
// /specification/{token}/{id}
$rt->put("/specification/*/*",SpecController::class);
// /specification/{token}/{id}
$rt->delete("/specification/*/*",SpecController::class);
// /specification/{token}
$rt->post('/specification/*',SpecController::class);
// /reference[/{page}[/search/{search}[/order/{direction}/sort{column}]]]
$rt->get("/reference{/*{/search/*{/order/*/sort/*}}}",ReferenceHierarchyController::class, ['ref']);
// /reference/{token}
$rt->post('/reference/*', ReferenceHierarchyController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}

//TODO add /tye/*/model{/*} route