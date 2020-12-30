<?php require_once __DIR__ . "/../app/_config/index.php";

use Http\Auth;

print_r(Auth::verifyToken());
