<?php require_once __DIR__ . "/../app/_config/index.php";

use Http\Auth;
use Http\Response;

// Update secret
Auth::generateSecret();
Response::message(['success' => ['message' => 'Secret has been updated!']]);
