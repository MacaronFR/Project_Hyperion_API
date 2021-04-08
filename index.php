<?php

use \Hyperion\API\{OAuthController,ConnectionController,StoreController};
use \Hyperion\API\Router;

require_once "autoload.php";

$rt = new Router();
$rt->get("/token/*/*/*/*", new OAuthController());
$rt->get("/token/*/*", new OAuthController());
$rt->get("/connect/*/*/*/*", new ConnectionController());
$rt->get("/store", new StoreController());
$rt->get("/store/*", new StoreController());