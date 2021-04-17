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
$rt->get("/store", StoreController::class);
$rt->get("/store/*", StoreController::class);
//Profile
$rt->get("/me/*", ProfileController::class);
//Category
$rt->get("/category", CategoryController::class);
$rt->get("/category/*", CategoryController::class);
$rt->post("/category/*", CategoryController::class);
$rt->put("/cat", CategoryController::class);
//Type
$rt->get("/category/type/*",TypeController::class);
$rt->get("/category/type/*/*",TypeController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}