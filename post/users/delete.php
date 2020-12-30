<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;

// Check for valid token
$form = Auth::verifyToken();

// Check for additional params
$posted = Request::allPost();

$form['role'] === 'agent' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to delete users!',
      ],
    ],
    405
  );

// Check if leadID posted
if (isset($posted['agentID'])) {
  // Remove user
  $form['role'] === 'owner' && $db->where('brand', $form['brand']);
  $db->where('agentID', $posted['agentID']);
  $db->delete(TABLE[1]);

  $db->getLastErrno() &&
    Response::message(['error' => ['message' => $db->getLastError()]], 406);

  // Remove leads
  $form['role'] === 'owner' && $db->where('brand', $form['brand']);
  $db->where('agentID', $posted['agentID']);
  $db->delete(TABLE[0]);

  if (!$db->getLastErrno()) {
    Response::message([
      'success' => [
        'message' => 'User & leads deleted!',
      ],
    ]);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
