<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Http\Request;
use Utilities\Rates;

$form = Auth::verifyToken();

// Check for additional params
$params = Request::allGet();

// Prevent unauthorized searches
if ($form['role'] !== 'admin') {
  $only30Days = true;
}
// Allow only owners and admin to override
if ($form['role'] !== 'agent') {
  !empty($params) &&
    isset($params['agentID']) &&
    ($form['agentID'] = $params['agentID']);
}

//# Get agent name

$db->where('agentID', $form['agentID']);
$agentName = $db->getOne(TABLE[1], ['fname', 'lname']);

//# Get all leads

// Include search params
include "../../app/partials/search.php";
$db->where('agentID', $form['agentID']);
$db->orderBy("submitted", "desc");
$leads = $db->get(TABLE[0]);

$db->count === 0 &&
  Response::message(['success' => ['message' => 'No results available']]);

// Get success rate for leads
$pos = 0;
foreach ($leads as $lead) {
  $lead['status'] === 'Call again' && $pos++;
}
$rate = ($pos / count($leads)) * 100;

//# Get MOM Rate

$months = Rates::getAgents($form, $db, false);

//# Set success rate
foreach ($months as $key => $month) {
  $months[$key]['successRate'] = Rates::success(
    $month['success'],
    $month['total']
  );
  unset($months[$key]['success']);
}

// Get MoM
if (isset($months[1])) {
  $momRate = Rates::MoM($months[0]['successRate'], $months[1]['successRate']);
} else {
  $momRate = 'NA';
}

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'agent' => $form['agentID'],
      'name' => $agentName['fname'] . ' ' . $agentName['lname'],
      'total' => count($leads),
      'successRate' => round($rate, 2),
      'leads' => $leads,
      'MoM' => $momRate,
      'monthly' => $months,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
