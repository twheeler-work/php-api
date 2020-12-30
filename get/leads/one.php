<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Http\Request;

$form = Auth::verifyToken();

// Check for params
$params = Request::allGet();

// Prevent unauthorized searches
if ($form['role'] !== 'admin') {
  $only30Days = true;
}

// Allow only owners & admins to view other leads
$form['role'] === 'owner' && ($restrictBrand = true);
$form['role'] === 'agent' && ($restrictAgent = true);

// Include search params
include "../../app/partials/search-joined.php";

//# Return lead
$db->join(TABLE[1] . " u", 'l.agentID=u.agentID', 'LEFT');
isset($restrictBrand) && $db->where('l.brand', $form['brand']);
isset($restrictAgent) && $db->where('l.agentID', $form['agentID']);
$lead = $db->getOne(
  TABLE[0] . ' l',
  "l.*, CONCAT(u.fname,' ',u.lname) as name"
);

// Send empty message
$db->count === 0 &&
  Response::message([
    'success' => [
      'message' => "No leads found with id: " . $params['leadID'],
    ],
  ]);

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'lead' => $lead,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
