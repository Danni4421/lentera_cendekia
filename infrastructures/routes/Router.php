<?php

namespace Infrastructures\Routes;

use Infrastructures\Contracts\Renderable;
use Infrastructures\Routes\Route;

class Router
{
  /** @var Route[] */
  private static $routes;

  public static function post($path, $callback, $method = 'index')
  {
    static::$routes[] = new Route(
      method: "POST",
      path: $path,
      callback: $callback,
      instance_method: $method
    );
  }

  public static function get($path, $callback, $method = 'index')
  {
    static::$routes[] = new Route(
      method: "GET",
      path: $path,
      callback: $callback,
      instance_method: $method
    );
  }

  /**
   * Boot all the route and match with specific path
   *
   * @return void
   */
  public static function resolve($path)
  {
    foreach (static::$routes as $route) {
      # Expected value from match method with parameter path
      ["status" => $status, "data" => $data] = $route->match($path);

      if ($status) {
        $result = null;

        if (isset($data["parameter"]["query"]) or isset($data["parameter"]["path"])) {
          $result = $route->resolve_callback($data["parameter"]);
        } else {
          $result = $route->resolve_callback();
        }

        if ($result instanceof Renderable) {
          $result->render();
        }

        exit;
      }
    }

    $not_found_page = view('not-found-page');
    $not_found_page->render();
  }
}
