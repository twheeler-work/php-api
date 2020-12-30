<?php require_once __DIR__ . "/../../app/_config/index.php";

use Http\Auth;
use Http\Response;
use Http\Request;
use Utilities\Rates;

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

if ($form['role'] === 'admin') {
  // Get all parameters
  if (Request::get('brand')) {
    $form['brand'] = Request::get('brand');
  }
  $returnAll = true;
}

// Select columns
$columns = [
  "agentID",
  "CONCAT(fname,' ',lname) as name",
  "fname",
  "lname",
  "email",
  "phone",
  "brand",
  "role",
];

//# Get all users
!isset($returnAll) && $db->where('brand', $form['brand']);
$users = $db->get(TABLE[1], null, $columns);

//# Get User rate

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

//? --------------------------------------
//? MoM Agent Rates
//? --------------------------------------

isset($returnAll)
  ? ($months = Rates::getAgents($form, $db, true, true))
  : ($months = Rates::getAgents($form, $db));

//# Set success rate
foreach ($months as $key => $month) {
  $months[$key]['successRate'] = Rates::success(
    $month['success'],
    $month['total']
  );
  unset($months[$key]['success']);
}

//# Set MoM
foreach ($months as $month) {
  $mom[$month['agentID']][] = [
    "total" => $month['total'],
    "successRate" => $month['successRate'],
  ];
}

//? --------------------------------------
//? Join to user array
//? --------------------------------------

foreach ($users as $key => $user) {
  if (isset($agentRate[$user['agentID']])) {
    $users[$key]['successRate'] = $agentRate[$user['agentID']]['successRate'];
    $users[$key]['total'] = $agentRate[$user['agentID']]['total'];
  } else {
    $users[$key]['total'] = 0;
  }
  // Set MoM
  if (isset($mom[$user['agentID']][1])) {
    $users[$key]['MoM'] = Rates::MoM(
      $mom[$user['agentID']][0]['successRate'],
      $mom[$user['agentID']][1]['successRate']
    );
  }
}

!$db->getLastErrno()
  ? Response::message([
    'success' => [
      'users' => $users,
    ],
  ])
  : Response::message(['error' => ['message' => $db->getLastError()]]);
