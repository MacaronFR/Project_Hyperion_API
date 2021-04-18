<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use \Hyperion\API\Router;
use \Hyperion\API\{ProductHierarchyController};

require_once "autoload.php";

$rt = new Router();
// /token/{client_id}/{client_secret}/{user_mail}/{user_passwd} => user token
$rt->get("/token/*/*/*/*", OAuthController::class);
// /token/{client_id}/{client_secret} => client token*
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
$rt->get("/type/*/product", ProductHierarchyController::class, ["product"]);
$rt->get("/type/*/product/*", ProductHierarchyController::class, ["product"]);
// /type/{id_type}/
$rt->get("/type/*",TypeController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}
