
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

// Set PROD url (Email & CORS)
$prodURL = 'https://some-site.com'; // <- Don't forget to set this

// Set DEV url (Email)
$devURL = '';

// DB connection info
define('DBDATA', include 'db.php');

// Set Session Timeout
define('TIMEOUT', 18000); // 30 mins 18000

// Set Stage Env
define('STAGE', true);

// Set local env
if ($_SERVER["REMOTE_ADDR"] === '127.0.0.1') {
  define('LOCAL', true);
} else {
  define('LOCAL', false);
}

// Set the default timezone
date_default_timezone_set('America/Chicago');

///////////////////////////////
// Env Handling
///////////////////////////////

if (!STAGE) {
  // PROD
  error_reporting(E_ALL ^ E_WARNING);
  define('FRONTEND_URL', $prodURL);
  define('CORS_URL', $prodURL);
} else {
  // DEV
  error_reporting(E_ALL);
  ini_set("display_errors", "On");
  define('FRONTEND_URL', $devURL);
  define('CORS_URL', '*');
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

