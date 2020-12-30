<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;

// Check for valid token
$form = Auth::verifyToken();

// Check for additional params
$posted = Request::allPost();

// Allow only owners & admins to view other leads
$form['role'] === 'owner' && ($restrictBrand = true);
$form['role'] === 'agent' && ($restrictAgent = true);

// Check if leadID posted
if (isset($posted['leadID'])) {
  isset($restrictBrand) && $db->where('brand', $form['brand']);
  isset($restrictAgent) && $db->where('agentID', $form['agentID']);
  $db->where('id', $posted['leadID']);
  $db->delete(TABLE[0]);

  if (!$db->getLastErrno()) {
    Response::message([
      'success' => [
        'message' => 'Lead deleted!',
      ],
    ]);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
