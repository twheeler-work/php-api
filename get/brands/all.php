<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;

$form = Auth::verifyToken();

// Only allow admins
$form['role'] === 'agent' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to view this information!',
      ],
    ],
    401
  );

// Prevent unauthorized searches
if ($form['role'] !== 'admin') {
  $only30Days = true;
}

// Include search params
include "../../app/partials/search.php";

//# Get all brand names
$db->groupBy('brand');
$db->where('brand IS NOT NULL');
$brands = $db->get(TABLE[1], null, ['brand']);

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'brands' => $brands,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
