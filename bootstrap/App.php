<?php

namespace Bootstrap;

use Infrastructures\Routes\Router;

class App
{
  public function run()
  {
    require_once __DIR__ . '/../routes/web.php';

    Router::resolve(path: isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "");
  }
}
