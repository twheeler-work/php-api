<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;
use Mail\Email;

// Check for valid token
Auth::verifyToken();

// Verify new password
if ($_POST) {
  // Sanitize input
  $form = Request::allPost();

  $db->where('email', $form['email']);
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

  // Create token for email verification
  $form['token'] = bin2hex(random_bytes(20));

  $db->startTransaction();

  $db->where('email', $form['email']);
  $db->update(TABLE[1], ['token' => $form['token']], 1);

  if (!$db->getLastErrno()) {
    if ((new Email())->forgotPassword($form)) {
      $db->commit();
      Response::message([
        'success' => [
          'message' => 'Reset instructions sent to ' . $form['email'],
        ],
      ]);
    } else {
      $db->rollback();
    }
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }
}
