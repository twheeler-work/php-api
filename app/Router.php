<?php

namespace Controllers;

use Http\Request;
use Http\Response;
use Exception;

class Router
{
  private $request;

  public function __construct()
  {
    $this->request = Request::getURI();
    // POST calls
    $isRequest = (new Request())->handleRequest();
    $isRequest && $this->backendRoutes();
  }

  /** ----------------------------
   ** Redirect URI
   * -----------------------------
   * Capture & clean URI to filter
   *  through accepted pages.
   *
   * @return string url
   */
  public function route()
  {
    // Get url var
    $uri = trim($this->request, "/");
    $uri = explode(".php", $uri);
    $uri = explode("/", $uri[0]);

    // If var empty set to index
    if (empty($uri[0])) {
      $uri[0] = HOME_DIR;
    }

    // Check for deep roots
    if (isset($uri[1])) {
      $uris = "";
      foreach ($uri as $i) {
        $uris .= $i . "/codes";
      }
      $uri[0] = substr($uris, 0, -1);
    }

    // print_r($this->pages);

    // Check if site exist
    if (in_array(strtolower($uri[0]), $this->getPages())) {
      return "/" . VIEWS . "$uri[0]/index.php";
    } else {
      return '/' . VIEWS . '404/index.php';
    }
  }

  /** ------------------------------
   ** Allow access to backend files
   * -------------------------------
   *  - Get available routes
   *  - Check with URI request
   *  - Verify header matches token
   */
  private function backendRoutes()
  {
    // Get url var
    $uri = trim($this->request, "/");
    $uri = explode(".php", $uri);
    $uri = explode("/", $uri[0]);

    // Check if file exist
    if (in_array(strtolower($uri[1]), $this->getPages('backend'))) {
      Request::verify_header();
    } else {
      Response::messageBox('error', 'Script not allowed!');
      die();
    }
  }

  /** ----------------------------
   ** API routes
   * -----------------------------
   * Takes an array of methods with
   *  their allowed paths.
   * - Checks the requested uri against
   *    the passed routes and directs
   *    accordingly.
   * - Includes the path to the method's
   *    index file.
   * - Returns the object ($req)
   *
   * @param [array] $routes
   * @param [string] $vs - version of api (root directory)
   * @return [object] $req
   */
  public function apiRoutes($routes, $vs = null)
  {
    $route = explode('/', $_GET['q'], 2);
    $query = explode("?", $route[1]);
    $route[1] = $query[0];

    // Match method
    if (isset($routes[$route[0]])) {
      $path = $route[1];
      $req = [
        "path" => $path,
        "route" => $path,
        "get" => isset($query[1]) ? $_GET : null,
        "post" => $_POST || null,
      ];

      // Check if path uses param
      foreach ($routes[$route[0]] as $r) {
        $rex = explode(":", $r);
        // Assign param path if exist
        if (isset($rex[1])) {
          // Get route start
          $src = explode("/", $r);
          array_pop($src);

          // Get path start
          $p = explode("/", $path);
          $param = array_pop($p);

          // Compare for differences
          if (!array_diff($src, $p)) {
            $req['route'] = $r;
            $req[$rex[1]] = $param;
            $path = $r;
          }
        }
      }

      // Standard routes
      if (in_array($path, $routes[$route[0]])) {
        $req = (object) $req;
        $vs ? ($vs .= "/") : "";
        include_once __DIR__ . "/../../api/$vs$route[0]/index.php";
      } else {
        Response::message(['error' => ["message" => "Invalid route!"]], 401);
      }
    } else {
      Response::message(['error' => ["message" => "Invalid method!"]], 401);
    }
  }

  /** ----------------------------
   ** Filter Pages
   * -----------------------------
   * Look through folders in
   *  received path & build array
   *  of pages to return.
   *
   * @param string path to page directory
   * @return array pages
   */
  private function getPages($return = null)
  {
    try {
      // Build for backend/frontend accessible files
      if ($return) {
        $return === "backend" && ($root = Request::getRoot() . "/" . SCRIPTS);
        $return === "api" && ($root = Request::getRoot() . "/" . API);
      } else {
        $root = Request::getRoot() . "/" . VIEWS;
      }

      $pages = [];
      $directories = [];
      $last_letter = $root[strlen($root) - 1];
      $root = $last_letter == '\\' || $last_letter == '/' ? $root : $root . '/';

      $directories[] = $root;

      // Get multilevel pages
      while (sizeof($directories)) {
        $dir = array_pop($directories);
        if ($handle = opendir($dir)) {
          while (false !== ($file = readdir($handle))) {
            // Avoid these directories
            if ($file == '.' || $file == '..' || $file == '.DS_Store') {
              continue;
            }
            // Remove component directories
            if ($file !== COMPONENT_DIR) {
              $file = $dir . $file;
              if (!$return && is_dir($file)) {
                $directory_path = $file . '/';
                array_push($directories, $directory_path);
              } elseif (is_file($file)) {
                // Remove root
                $file = str_replace($root, "", $file);
                // Remove extension
                $page = explode("/", $file);
                // Format for return
                if ($return) {
                  $page = explode(".php", $file);
                  array_push($pages, $page[0]);
                } else {
                  array_pop($page);
                  $page = implode('/', $page);
                  array_push($pages, $page);
                }
              }
            }
          }
          closedir($handle);
        }
      }
      return $return ? $pages : array_unique($pages);
    } catch (Exception $e) {
      print_r($e);
    }
  }

  /** ----------------------------
   ** Format URI
   * -----------------------------
   * Return clean uri as path OR
   *  as page name.
   *
   * @param string $uri
   * @param boolean $returnName
   * @return string uri or name
   */
  public static function trimURI($returnName = false)
  {
    $page = rtrim(Request::getURI(), '/');
    $page = explode(".php", $page);

    $page = explode("/", $page[0]);
    $pageName = array_pop($page);

    $root = "";
    foreach ($page as $i) {
      $root .= $i . "/";
    }
    // Return page name
    if ($returnName) {
      empty($pageName) && ($pageName = HOME_DIR);
      $root = $pageName;
    }
    return $root;
  }
}