<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController,ProfileController};
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
if(!$rt->getRouted()){
	response(404, "Not Found");
}