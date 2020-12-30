<?php require_once __DIR__ . "/../app/_config/index.php";

use Http\Auth;
use Http\Response;

// Create token session validation
Response::message(Auth::getToken());
