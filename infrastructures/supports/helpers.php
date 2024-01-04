<?php

use Infrastructures\Factories\View as ViewFactory;
use Infrastructures\Contracts\Renderable;

if (!function_exists('view')) {
  /**
   * @param string $view
   * @param array $data
   * @return Renderable
   */
  function view($view = null, $data = [])
  {
    $factory = new ViewFactory($view, $data);
    return $factory;
  }
}
