<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController};
use \Hyperion\API\Router;

require_once "autoload.php";

$rt = new Router();
$rt->get("/token/*/*/*/*", OAuthController::class);
$rt->get("/token/*/*", OAuthController::class);
$rt->get("/connect/*/*/*/*", ConnectionController::class);
$rt->get("/store", StoreController::class);
$rt->get("/store/*", StoreController::class);