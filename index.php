<?php require_once "app/_config/index.php";

// Bring in frontend
require __DIR__ . $router->route();

// Close any open connections
isset($db) && $db->disconnect();
