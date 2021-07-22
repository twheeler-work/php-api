<?php $CORS = "get";
require_once __DIR__ . "/../../../app/core/_config/index.php";

use Https\Auth;
use Https\Request;
use Https\Response;
use Utilities\Helper;

$mode = Request::getAPI('mode');

//* ---------------------------------------------
//# Direct to RESTApi Mode
//* ---------------------------------------------

if ($mode) {
  // Get token
  if (isset($mode["token"])) {
    Auth::verifyOATH();
    $return = Auth::getToken(['allowAPI' => true]);
    $return ? ($status = 200) : ($status = 400);
    $description =
      'Validate authentication method and return session token for GET access.';
  }
}

//* ---------------------------------------------
//# Return results
//* ---------------------------------------------

$message = isset($return)
  ? [
    'name' => 'Affiliates Portal Authentication',
    'description' => isset($description) ? $description : null,
    'timer' => Helper::timer() . ' seconds',
    'token' => isset($return) ? $return : null,
  ]
  : [
    'name' => 'Affiliates Portal Authentication',
    'description' => 'Validate authentication methods',
    'endpoints' => ['/token'],
  ];

Response::message($message, isset($return) ? $status : 200);