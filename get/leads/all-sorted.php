<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Utilities\Rates;

$form = Auth::verifyToken();

// Only allow admins
$form['role'] !== 'admin' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to view this information!',
      ],
    ],
    401
  );

//# Get all brand names
$db->groupBy('brand');
$brands = $db->get(TABLE[0], null, ['brand']);

// Return if empty
$db->count === 0 &&
  Response::message(['success' => ['message' => 'No results available']]);

//# Get brand rate

// Include search params
include "../../app/partials/search.php";

$select = [
  'brand',
  "SUM(CASE WHEN `status`='Call Again' THEN 1 ELSE 0 END) as success",
  'COUNT(*) as total',
];
$db->groupBy('brand');
$brandRate = $db->get(TABLE[0], null, $select);

foreach ($brandRate as $i => $v) {
  $brandRate[$v['brand']] = [
    'successRate' => Rates::success($v['success'], $v['total']),
    'total' => $v['total'],
  ];
  unset($brandRate[$i]);
}

//# Get Agent rate

// Include search params
include "../../app/partials/search.php";

$select = [
  'agentID',
  "SUM(CASE WHEN `status`='Call Again' THEN 1 ELSE 0 END) as success",
  'COUNT(*) as total',
];
$db->groupBy('agentID');
$agentRate = $db->get(TABLE[0], null, $select);

foreach ($agentRate as $i => $v) {
  $agentRate[$v['agentID']]['successRate'] = Rates::success(
    $v['success'],
    $v['total']
  );
  $agentRate[$v['agentID']]['total'] = $v['total'];
  unset($agentRate[$i]);
}

//# Get agents by brand
$agents = [];
foreach ($brands as $brand) {
  // Include search params
  include "../../app/partials/search-joined.php";

  // Pass leads with agent name
  $agents[$brand['brand']]['brand'] = $brand['brand'];
  $db->join(TABLE[1] . " u", 'l.agentID=u.agentID', 'LEFT');
  $db->where('l.brand', $brand['brand']);
  $db->orderBy("id", "desc");
  $agents[$brand['brand']]['leads'] = $db->get(
    TABLE[0] . ' l',
    null,
    "l.*, CONCAT(u.fname,' ',u.lname) as name, u.role as role"
  );
}

//# Totals for brand
foreach ($agents as $agent) {
  $values = [];
  foreach ($agent['leads'] as $lead) {
    // Set agent id
    $agents[$lead['brand']]['agents'][$lead['agentID']]['agent'] =
      $lead['agentID'];
    // Set total leads
    $agents[$lead['brand']]['agents'][$lead['agentID']]['total'] =
      $agentRate[$lead['agentID']]['total'];
    // Set Success rate
    $agents[$lead['brand']]['agents'][$lead['agentID']]['rate'] =
      $agentRate[$lead['agentID']]['successRate'];
    // Insert leads
    $agents[$lead['brand']]['agents'][$lead['agentID']]['leads'][] = $lead;
  }
  if (isset($lead)) {
    unset($agents[$lead['brand']]['leads']);
  }
}

//? --------------------------------------
//? MoM Brand Rates
//? --------------------------------------

$brandMonths = Rates::getBrand($form, $db, true);

if (count($brandMonths) > 1) {
  foreach ($brandMonths as $brand) {
    $values[$brand['brand']][] = [
      'month' => $brand['month'],
      'total' => $brand['total'],
      'successRate' => Rates::success($brand['success'], $brand['total']),
    ];
    $brandRate[$brand['brand']]['monthly']['months'] = $values[$brand['brand']];

    //# Set MoM for Brand
    if (count($brandRate[$brand['brand']]['monthly']['months']) > 1) {
      $brandMomRate = Rates::MoM(
        $brandRate[$brand['brand']]['monthly']['months'][0]['successRate'],
        $brandRate[$brand['brand']]['monthly']['months'][1]['successRate']
      );
      $brandRate[$brand['brand']]['monthly']['MoM'] = $brandMomRate;
    } else {
      $brandRate[$brand['brand']]['monthly']['months'] = 'NA';
      $brandRate[$brand['brand']]['monthly']['MoM'] = 'NA';
    }
  }
} else {
  $brandRate = [];
}

$agentMonths = Rates::getAgents($form, $db, true, true);

if (count($agentMonths) > 1) {
  $values = [];
  foreach ($agentMonths as $agent) {
    $values[$agent['agentID']]['months'][] = [
      'month' => $agent['month'],
      'total' => $agent['total'],
      'successRate' => Rates::success($agent['success'], $agent['total']),
    ];
  }

  //# Set MoM for Agents
  foreach ($values as $i => $value) {
    if (count($value['months']) > 1) {
      $values[$i]['MoM'] = Rates::MoM(
        $value['months'][0]['successRate'],
        $value['months'][1]['successRate']
      );
    } else {
      $values[$i]['months'] = 'NA';
      $values[$i]['MoM'] = 'NA';
    }
  }
  $agentMonths = $values;
  unset($values);
}

// Apply to agents
foreach ($agents as $brand => $value) {
  if (isset($value['agents'])) {
    foreach ($value['agents'] as $agent => $value) {
      $agents[$brand]['agents'][$agent]['monthly'] = $agentMonths[$agent];
    }
  }
}

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'brandRates' => $brandRate,
      'brands' => $agents,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
