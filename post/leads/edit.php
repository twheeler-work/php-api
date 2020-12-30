<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;

// Check for valid token
$form = Auth::verifyToken();

// Check for additional params
$posted = Request::allPost(true);

// Allow only owners & admins to view other leads
$form['role'] === 'owner' && ($restrictBrand = true);
$form['role'] === 'agent' && ($restrictAgent = true);

// Format date & time
isset($posted['date']) &&
  ($posted['date'] = date('Y-m-d', strtotime($posted['date'])));
isset($posted['time']) &&
  ($posted['time'] = date('g:i a', strtotime($posted['time'])));

// Get original lead
if ($_POST) {
  $db->where('id', $posted['id']);
  isset($restrictBrand) && $db->where('brand', $form['brand']);
  isset($restrictAgent) && $db->where('agentID', $form['agentID']);
  $db->getOne(TABLE[0]);

  // Exit if lead doesn't exist
  $db->count === 0 &&
    Response::message([
      'error' => [
        'message' => 'This lead does not exists!',
      ],
    ]);

  $db->startTransaction();

  $db->where('id', $posted['id']);
  isset($restrictBrand) && $db->where('brand', $form['brand']);
  isset($restrictAgent) && $db->where('agentID', $form['agentID']);
  $db->update(TABLE[0], $posted, 1);

  if (!$db->getLastErrno()) {
    $db->commit();
    Response::message([
      'success' => [
        'message' => $posted['id'] . ' has been updated!',
      ],
    ]);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
