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
// /token/{client_id}/{client_secret}/{user_mail}/{user_passwd} => user token
$rt->get("/token/*/*/*/*", OAuthController::class);
// /token/{client_id}/{client_secret} => client token
$rt->get("/token/*/*", OAuthController::class);
// /connect/{client_id}/{client_secret}/{user_mail}/{user_passwd}
$rt->get("/connect/*/*/*/*", ConnectionController::class);
// /disconnect/{user_token}
$rt->delete("/disconnect/*", ConnectionController::class);
// /inscription/{client_token}
// {}
$rt->post("/inscription/*", ConnectionController::class);
//Store
// /store[/{page}]
$rt->get("/store", StoreController::class);
$rt->get("/store/*", StoreController::class);
//Profile
// /me/{user_token}
$rt->get("/me/*", ProfileController::class);
$rt->put("/me/*", ProfileController::class);
// /profile/{token}/{user_id}
$rt->get("/profile/*/*",ProfileController::class);
$rt->put("/profile/*/*",ProfileController::class);
// /category[/{page}]
$rt->get("/category", CategoryController::class);
$rt->get("/category/*", CategoryController::class);
// /category/{user_token}
// {"name": <category_name>}
$rt->post("/category/*", CategoryController::class);
// /category/{client_token}/{id_cat}
// {"name": <new_category_name>}
$rt->put("/category/*/*", CategoryController::class);
// /category/{client_token}/{id_cat}
$rt->delete("/category/*/*", CategoryController::class);
//Type
$rt->get("/type", TypeController::class);
$rt->get("/type_cat", TypeController::class, ['cat']);
// /category/type/{id_category}[/{page}]
$rt->get("/category/*type/",MarkModelController::class, ["type"]);
$rt->get("/category/*/type/*",MarkModelController::class, ["type"]);
// /type/{id_type}/product[/{page}]
$rt->get("/type/*/product", ProductHierarchyController::class, ["type_product"]);
$rt->get("/type/*/product/*", ProductHierarchyController::class, ["type_product"]);
// /type/{id_type}/reference[/{page}]
$rt->get("/type/*/reference", ReferenceHierarchyController::class, ["type_reference"]);
$rt->get("/type/*/reference/*", ReferenceHierarchyController::class, ["type_reference"]);
// /mark/{mark_name}/product[/{page}]
$rt->get("/mark/*/product", ProductHierarchyController::class, ["mark_product"]);
$rt->get("/mark/*/product/*", ProductHierarchyController::class, ["mark_product"]);
// /mark/{mark_name}/reference[/{page}]
$rt->get("/mark/*/reference", ReferenceHierarchyController::class, ["mark_reference"]);
$rt->get("/mark/*/reference/*", ReferenceHierarchyController::class, ["mark_reference"]);
// /type/{id_type}/mark/{mark_name}/product[/{page}]
$rt->get("/type/*/mark/*/product", ProductHierarchyController::class, ["type_mark_product"]);
$rt->get("/type/*/mark/*/product/*", ProductHierarchyController::class, ["type_mark_product"]);
// /type/{id_type}/mark/{mark_name}/reference[/{page}]
$rt->get("/type/*/mark/*/reference", ReferenceHierarchyController::class, ["type_mark_reference"]);
$rt->get("/type/*/mark/*/reference/*", ReferenceHierarchyController::class, ["type_mark_reference"]);

$rt->get("/type/*/mark", MarkModelController::class, ["type_mark"]);
$rt->get("/type/*/mark/*", MarkModelController::class, ["type_mark"]);

$rt->get("/mark/*/model", MarkModelController::class, ["mark_model"]);
$rt->get("/mark/*/model/*", MarkModelController::class, ["mark_model"]);

$rt->get("/type/*/mark/*/model", MarkModelController::class, ["type_mark_model"]);
$rt->get("/type/*/mark/*/model/*", MarkModelController::class, ["type_mark_model"]);

$rt->get("/mark", MarkModelController::class, ["mark"]);
$rt->get("/mark/*", MarkModelController::class, ["mark"]);

$rt->get("/model", MarkModelController::class, ["model"]);
$rt->get("/model/*", MarkModelController::class, ["model"]);

$rt->get("/model/*/product", ProductHierarchyController::class, ['model_product']);
$rt->get("/model/*/product/*", ProductHierarchyController::class, ['model_product']);

$rt->get("/mark/*/model/*/product", ProductHierarchyController::class, ['mark_model_product']);
$rt->get("/mark/*/model/*/product/*", ProductHierarchyController::class, ['mark_model_product']);

$rt->get("/type/*/model/*/product", ProductHierarchyController::class, ['type_model_product']);
$rt->get("/type/*/model/*/product/*", ProductHierarchyController::class, ['type_model_product']);

$rt->get("/type/*/mark/*/model/*/product", ProductHierarchyController::class, ['type_mark_model_product']);
$rt->get("/type/*/mark/*/model/*/product/*", ProductHierarchyController::class, ['type_mark_model_product']);

$rt->get("/model/*/reference", ReferenceHierarchyController::class, ['model_reference']);

$rt->get("/mark/*/model/*/reference", ReferenceHierarchyController::class, ['mark_model_reference']);

$rt->get("/type/*/model/*/reference", ReferenceHierarchyController::class, ['type_model_reference']);

$rt->get("/type/*/mark/*/model/*/reference", ReferenceHierarchyController::class, ['type_mark_model_reference']);

// Specification route

$rt->get("/specification/*",SpecController::class);

if(!$rt->getRouted()){
	response(404, "Not Found");
}
