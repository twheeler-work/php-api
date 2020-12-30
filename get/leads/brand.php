<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Http\Request;
use Utilities\Rates;

$form = Auth::verifyToken();

// Prevent unauthorized searches
if ($form['role'] !== 'admin') {
  $only30Days = true;
}

// Allow only owners & admins
$form['role'] === 'agent' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to view this information!',
      ],
    ],
    401
  );

// Check for brand override, for admin
$params = Request::allGet();
$form['role'] === 'admin' &&
  !empty($params) &&
  ($form['brand'] = $params['brand']);

// Include search params
include "../../app/partials/search-joined.php";

// Pass leads with agent name
$db->join(TABLE[1] . " u", 'l.agentID=u.agentID', 'LEFT');
$db->where('l.brand', $form['brand']);
$db->orderBy("id", "desc");
$leads = $db->get(
  TABLE[0] . ' l',
  null,
  "l.*, CONCAT(u.fname,' ',u.lname) as name, u.role as role"
);

// Return if empty
$db->count === 0 &&
  Response::message(['success' => ['message' => 'No results available']]);

//# Group by agents
$agents = [];
foreach ($leads as $lead) {
  $agents[$lead['agentID']]['agent'] = $lead['agentID'];
  $agents[$lead['agentID']]['leads'][] = $lead;
}

//# Set totals for each agent
foreach ($agents as $agent) {
  // Agent total
  $agents[$agent['agent']]['total'] = count($agent['leads']);

  // Get Rate
  $pos = 0;
  foreach ($agent['leads'] as $lead) {
    $lead['status'] === 'Call Again' && $pos++;
  }
  // Success Rate
  $agents[$agent['agent']]['successRate'] = Rates::success(
    $pos,
    $agents[$agent['agent']]['total']
  );
}

//? --------------------------------------
//? MoM Brand Rates
//? --------------------------------------

$months = Rates::getBrand($form, $db);

//# Set success rate
foreach ($months as $key => $month) {
  $months[$key]['successRate'] = Rates::success(
    $month['success'],
    $month['total']
  );
  unset($months[$key]['success']);
}

//# Set MoM
if (isset($months[1])) {
  $momRate = Rates::MoM($months[0]['successRate'], $months[1]['successRate']);
} else {
  $momRate = 'NA';
}

//? --------------------------------------
//? MoM Agent Rates
//? --------------------------------------

$monthsByAgent = Rates::getAgents($form, $db);

foreach ($monthsByAgent as $i => $agent) {
  $values[$agent['agentID']][] = $agent;

  // Check for 2 months to compare
  if (count($values[$agent['agentID']]) > 1) {
    //# Set success rate
    foreach ($values[$agent['agentID']] as $month) {
      $monthly['months'][] = [
        'month' => $month['month'],
        'total' => $month['total'],
        'successRate' => Rates::success($month['success'], $month['total']),
      ];
      $agents[$lead['agentID']]['monthly'] = $monthly;
    }

    //# Set MoM
    $agentMomRate = Rates::MoM(
      $agents[$lead['agentID']]['monthly']['months'][0]['successRate'],
      $agents[$lead['agentID']]['monthly']['months'][1]['successRate']
    );
    $agents[$agent['agentID']]['MoM'] = $agentMomRate;
  } else {
    $agents[$agent['agentID']]['monthly'] = 'NA';
    $agents[$agent['agentID']]['MoM'] = 'NA';
  }
  unset($monthsByAgent[$i]);
}

// print_r($monthsByAgent);

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'brand' => $form['brand'],
      'brandTotal' => $db->count,
      'agents' => $agents,
      'MoM' => $momRate,
      'monthly' => $months,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
