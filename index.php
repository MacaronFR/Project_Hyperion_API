<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use \Hyperion\API\Router;
use \Hyperion\API\{ProductHierarchyController,ReferenceHierarchyController};

require_once "autoload.php";

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
// /category[/{page}]
$rt->get("/category", CategoryController::class);
$rt->get("/category/*", CategoryController::class);
// /category/{user_token}
// {"name": <category_name>}
$rt->post("/category/*", CategoryController::class);
// /category/{client_token}/{id_cat}
// {"name": <new_category_name>}
$rt->put("/category/*/*", CategoryController::class);
//Type
//
// /category/type/{id_category}[/{page}]
$rt->get("/category/type/*",ProductHierarchyController::class, ["type"]);
$rt->get("/category/type/*/*",ProductHierarchyController::class, ["type"]);
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
// /type/{id_type}/
$rt->get("/type/*",TypeController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}
