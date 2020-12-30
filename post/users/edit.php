<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;

// Check for valid token
$form = Auth::verifyToken();

// Check for additional params
$posted = Request::allPost();

// Allow only owners and admin to override
if ($form['role'] === 'owner' || $form['role'] === 'admin') {
  if (!empty($posted)) {
    // Editing agentID
    if (isset($posted['agentID'])) {
      $form['agentID'] = $posted['agentID'];
      unset($posted['agentID']);
    }
    // Allow only admin to set admin role
    if (
      isset($posted['role']) &&
      $posted['role'] === 'admin' &&
      $form['role'] !== 'admin'
    ) {
      Response::message(
        [
          'error' => [
            'message' => 'You are not authorized to set admins!',
          ],
        ],
        406
      );
    }
    // Allow only admin to edit brand
    if (
      isset($posted['brand']) &&
      $posted['brand'] !== $form['brand'] &&
      $form['role'] !== 'admin'
    ) {
      Response::message(
        [
          'error' => [
            'message' => 'You are not authorized to edit brand!',
          ],
        ],
        406
      );
    }
  }
} else {
  !empty($posted) &&
    (isset($posted['agentID']) ||
      isset($posted['role']) ||
      isset($posted['brand'])) &&
    Response::message(
      [
        'error' => [
          'message' => 'You are not authorized to edit this user!',
        ],
      ],
      406
    );
}

// Check for user
if ($_POST) {
  $db->where('agentID', $form['agentID']);
  $user = $db->getOne(TABLE[1]);

  // Exit if user doesn't exist
  $db->count === 0 &&
    Response::message(
      [
        'error' => [
          'message' => 'This user does not exists!',
        ],
      ],
      406
    );

  $db->startTransaction();

  $db->where('agentID', $form['agentID']);
  $db->update(TABLE[1], $posted, 1);

  if (!$db->getLastErrno()) {
    $db->commit();
    Response::message([
      'success' => [
        'message' => $form['agentID'] . ' has been updated!',
      ],
    ]);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
