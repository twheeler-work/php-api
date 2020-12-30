<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;

// Check for valid token
$form = Auth::verifyToken();

// Allow only owners and admin to override
if ($form['role'] !== 'admin') {
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to edit brands!',
      ],
    ],
    406
  );
}

// Check for additional params
$form = Request::allPost();

if ($_POST) {
  // Update brand name in user table
  $db->where('brand', $form['old']);
  $brands = $db->update(TABLE[1], ['brand' => $form['brand']]);

  if ($db->getLastErrno()) {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }

  // Update brand name in leads table
  $db->where('brand', $form['old']);
  $brands = $db->update(TABLE[0], ['brand' => $form['brand']]);

  if (!$db->getLastErrno()) {
    $db->commit();
    Response::message([
      'success' => [
        'message' =>
          $form['old'] . ' has been updated to ' . $form['brand'] . '!',
      ],
    ]);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
