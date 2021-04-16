<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController,CategoryController};
use \Hyperion\API\Router;

require_once "autoload.php";

$rt = new Router();
$rt->get("/token/*/*/*/*", OAuthController::class);
$rt->get("/token/*/*", OAuthController::class);
$rt->get("/connect/*/*/*/*", ConnectionController::class);
$rt->delete("/disconnect/*", ConnectionController::class);
$rt->post("/inscription/*", ConnectionController::class);
$rt->get("/store", StoreController::class);
$rt->get("/store/*", StoreController::class);
$rt->get("/me/*", ProfileController::class);
$rt->get("/category", CategoryController::class);
$rt->get("/category/*", CategoryController::class);
$rt->post("/category", CategoryController::class);
$rt->put("/cat", CategoryController::class);
if(!$rt->getRouted()){
	response(404, "Not Found");
}