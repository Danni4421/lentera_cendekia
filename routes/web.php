<?php

use App\Controllers\HomeController;
use Infrastructures\Routes\Router;

/**
 * Defines the route that runs on the web
 */

Router::get("/", HomeController::class, 'index');
