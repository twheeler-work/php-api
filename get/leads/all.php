<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Http\Request;

$form = Auth::verifyToken();

// Get all parameters
$params = Request::allGet();

// Prevent unauthorized searches
if ($form['role'] !== 'admin') {
  $only30Days = true;
}
if ($form['role'] === 'owner') {
  unset($params['brand']);
}
if ($form['role'] === 'agent') {
  unset($params['agentID']);
  unset($params['brand']);
}

// Set user restrictions
$form['role'] === 'owner' && ($restrictBrand = true);
$form['role'] === 'agent' && ($restrictAgent = true);

// Include search params
include "../../app/partials/search-joined.php";

//# Get all leads

// Restricted search
isset($restrictBrand) && $db->where('l.brand', $form['brand']);
isset($restrictAgent) && $db->where('l.agentID', $form['agentID']);
// Admin search
isset($params['brand']) && $db->where('l.brand', $params['brand']);
isset($params['agentID']) && $db->where('l.agentID', $params['agentID']);
$db->join(TABLE[1] . " u", 'l.agentID=u.agentID', 'LEFT');
$db->orderBy("l.submitted", "desc");
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

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'total' => $db->count,
      'leads' => $leads,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
