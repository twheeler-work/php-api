<?php require_once __DIR__ . "/../../app/Config/index.php";

use Controllers\Router;

/** --------------------------------
 ** Set allowed routes here
 * ---------------------------------
 * Add paths to desired method
 * - EX:
 *     'get' => ['test/test1', 'test/test2']
 *     'post' => ['test/', 'example/test/here']
 *
 *! Methods must exist in target directory
 * @param [array] paths
 * @param [string] version root for api directory - optional
 * @return includes with object $req
 */
(new Router())->apiRoutes(
  [
    'auth' => ['token'],
    'get' => ['codes/all', 'codes/find/:key', 'users'],
    'post' => ['codes', 'users'],
    'delete' => ['codes/:id'],
  ],
  "v2"
);