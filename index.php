<?php

require_once "Controllers/OAuthController.php";
require_once "Routers.php";

use Hyperion\API\Router;
use Hyperion\API\OAuthController;
$rt = new Router();
$rt->get("/token/*/*/*/*", new OAuthController());
$rt->get("/token/*/*", new OAuthController());