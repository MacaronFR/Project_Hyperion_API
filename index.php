<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use \Hyperion\API\Router;
use \Hyperion\API\{TypeController};

require_once "autoload.php";

$rt = new Router();
// /token/{clientid}/{clientsecret}/{usermail}/{userpasswd} => user token
$rt->get("/token/*/*/*/*", OAuthController::class);
// /token/{clientid}/{clientsecret} => client token*
$rt->get("/token/*/*", OAuthController::class);
// /connect/{clientid}/{clientsecret}/{usermail}/{userpasswd}
$rt->get("/connect/*/*/*/*", ConnectionController::class);
// /disconnect/{user_token}
$rt->delete("/disconnect/*", ConnectionController::class);
// /inscription/{client_token}
// {}
$rt->post("/inscription/*", ConnectionController::class);
//Store
// /store
$rt->get("/store", StoreController::class);
// /store/{page}
$rt->get("/store/*", StoreController::class);
//Profile
// /me/{user_token}
$rt->get("/me/*", ProfileController::class);
// /category
$rt->get("/category", CategoryController::class);
// /category/{id_cat}
$rt->get("/category/*", CategoryController::class);
// /category/{user_token}
// {"name": <category_name>}
$rt->post("/category/*", CategoryController::class);
// /category/{client_token}/{id_cat}
// {"name": <new_category_name>}
$rt->put("/category/*/*", CategoryController::class);
//Type
$rt->get("/category/type/*",TypeController::class);
$rt->get("/category/type/*/*",TypeController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}
