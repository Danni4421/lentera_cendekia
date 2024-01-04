<?php

namespace Infrastructures\Factories;

use Infrastructures\Contracts\Renderable;

class View implements Renderable
{
  private string $view;
  private array $data;
  private const base_view_path = "../resources/views";

  public function __construct(string $view = 'index', array $data = [])
  {
    $this->view = $this->root_view_path($view);
    $this->data = $data;
  }


  public function render()
  {
    if (!file_exists($this->view)) {
      throw new \Exception('View Not Found');
    }

    ob_start();
    extract($this->data);
    ob_get_clean();

    require_once $this->view;
  }

  private function root_view_path($view)
  {
    $exploded_view_path = explode('.', $view);
    $target_view = end($exploded_view_path) . '.view.php';
    $concatenate_view_directories = implode(DIRECTORY_SEPARATOR, array_slice($exploded_view_path, 0, -1));
    return self::base_view_path . DIRECTORY_SEPARATOR . $concatenate_view_directories . DIRECTORY_SEPARATOR . $target_view;
  }
}
