<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;
use Utilities\Export;

// Check for valid token
$auth = Auth::verifyToken();

// Check for additional params
$params = Request::allGet();

$auth['role'] !== 'admin' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to delete users!',
      ],
    ],
    405
  );

// Include search params
include "../../app/partials/search-joined.php";

//# Get all leads

$db->join(TABLE[1] . " u", 'l.agentID=u.agentID', 'LEFT');
$db->orderBy("id", "desc");
$leads = $db->get(
  TABLE[0] . ' l',
  null,
  "l.*, CONCAT(u.fname,' ',u.lname) as name"
);

// Send empty message
$db->count === 0 &&
  Response::message([
    'success' => [
      'message' => "No leads found!",
    ],
  ]);

// Return last error
$db->getLastErrno() &&
  Response::message(['error' => ['message' => $db->getLastError()]]);

// Return export
print_r((new Export($leads, "d2d-leads"))->csv());
