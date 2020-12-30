
<?php
///////////////////////////////
// Vendor Library
///////////////////////////////

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Utilities\DB;

///////////////////////////////
// Project Variables
///////////////////////////////

/**
 * DB Table
 * @var \Array
 * Enter table names as array for easy changes
 *
 * 0 - leads
 *
 * 1 - users
 */
define('TABLE', ["leads", "users"]);

///////////////////////////////
// Set Globals
///////////////////////////////

// DB connection info
define('DBDATA', include 'db.php');

// Set Session Timeout
// define('TIMEOUT', 1800); // 30 mins 1800
define('TIMEOUT', 18000); // 30 mins 1800

// Set Stage Env
define('STAGE', true);

// Set local env
if ($_SERVER["REMOTE_ADDR"] === '127.0.0.1') {
  define('LOCAL', true);
  define('FRONTEND_URL', '*'); // DEV
} else {
  define('LOCAL', false);
  // define('FRONTEND_URL', 'http://projecta.nrg.com'); // PROD
  define('FRONTEND_URL', '*'); // DEV
}

// Set the default timezone
date_default_timezone_set('America/Chicago');

///////////////////////////////
// Error Handling
///////////////////////////////

if (!STAGE) {
  error_reporting(E_ALL ^ E_WARNING);
} else {
  error_reporting(E_ALL);
  ini_set("display_errors", "On");
}

///////////////////////////////
// CORS Setup
///////////////////////////////

include_once __DIR__ . '/../components/headers.php';

///////////////////////////////
// Load Environmental Vars
///////////////////////////////

(new DotEnv(__DIR__ . "/../../"))->load();

///////////////////////////////
// Connect DB
///////////////////////////////

$db = (new DB())->start();

