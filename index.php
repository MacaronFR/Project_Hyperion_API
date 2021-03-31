<?php

use \Hyperion\API\{OAuthController,ConnectionController};
use \Hyperion\API\Router;

require_once "autoload.php";

$rt = new Router();
$rt->get("/token/*/*/*/*", new OAuthController());
$rt->get("/token/*/*", new OAuthController());
$rt->put("/connect/*/*", new ConnectionController());