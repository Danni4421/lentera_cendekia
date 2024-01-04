<?php

namespace Infrastructures\Routes;


use Closure;

class Route
{
  private string $method;
  private string $path;
  private Closure | string $callback;
  private string $instance_method;

  public function __construct($method, $path, $callback, $instance_method)
  {
    $this->method = $method;
    $this->path = $path;
    $this->callback = $callback;
    $this->instance_method = $instance_method;
  }

  public function get_instance_method()
  {
    return $this->instance_method;
  }

  /**
   * Matching the requested url with the route path
   *
   * @param string $request_uri
   * @return array
   */
  public function match($request_uri)
  {
    # Getting query parameter from request uri
    $query_parameter = $this->get_query_parameter($request_uri);
    # Getting full path exclude query parameter
    $request_uri = $this->get_full_path($request_uri);

    # Verify if there is some path parameter
    if ($path_parameter = $this->validate_path_parameter($request_uri, $this->path)) {

      # Convert route path into a regex for verifying a requested path is match or not with defined path
      $regexPattern = $this->convert_path_into_regex($this->path);

      # Verifying result of converted regex from path with requested path
      if (preg_match($regexPattern, $request_uri, $matches)) {
        return [
          "status" => true,
          "data" => [
            "parameter" => [
              "path" => $path_parameter,
              "query" => $query_parameter,
            ]
          ],
        ];
      } else {
        return [
          "status" => false,
        ];
      }
    } else {
      # If the path don't have path parameter it going to be here
      return [
        "status" => $request_uri == $this->path,
        "data" => [
          "parameter" => [
            "query" => $query_parameter,
          ]
        ]
      ];
    }
  }

  /**
   * @param array $args
   * @return mixed
   */
  public function resolve_callback($args = [])
  {
    # If the type of callback is a string, it's asume to be a class 
    # So it will be enter the if block below
    if (gettype($this->callback) == "string") {
      $callback = new $this->callback;
      $method = $this->instance_method;

      if (!empty($args)) {
        return $callback->$method($args);
      } else {
        return $callback->$method();
      }

      # If the type is object of Closure, it's asume that given an argument with a anonym function
    } elseif (gettype($this->callback) == "object" && $this->callback instanceof Closure) {
      return call_user_func_array($this->callback, $args);
    }
  }

  /**
   * Converter path into regular expression
   *
   * @param string $path
   * @return string
   */
  private function convert_path_into_regex($path)
  {
    # Replace any of this pattern {*} with ([^/]+) to match whatever is in it
    $regexPattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
      return '([^/]+)';
    }, $path);

    # After converting into regex next will be replace the string / with \/ to ensure regex pattern
    return '/^' . str_replace('/', '\/', $regexPattern) . '$/';
  }

  /**
   * @param string $request_uri
   * @return string
   */
  private function get_full_path($request_uri)
  {
    # Return back path from request uri
    return parse_url($request_uri)["path"];
  }

  /**
   * @param string $request_uri
   * @return array
   */
  private function get_query_parameter($request_uri)
  {
    # Return back query parameter from request uri
    $queries_str = parse_url($request_uri)["query"];
    parse_str($queries_str, $queries);
    return $queries;
  }

  /**
   * @param string $requested_path
   * @param string $path
   * @return array
   */
  private function validate_path_parameter($requested_path, $path)
  {
    # This function is validating is there any path parameter with in the path and requested path

    # First is exploding an array and filter an empty array so it will got exploded path
    $exploded_path = array_filter(explode('/', $path));
    $exploded_requested_path = array_filter(explode('/', $requested_path));

    $path_parameter = [];

    # After exploding path it will check every single of subpath if it match will insert the value in path_parameter
    foreach ($exploded_path as $key => $path) {
      if (preg_match('/^\{(.*)\}$/', $path, $matches)) {
        $path_parameter[$matches[1]] = $exploded_requested_path[$key];
      }
    }

    return $path_parameter;
  }
}
