<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Request;
use Http\Response;

// Verify new password
if ($_POST) {
  // Sanitize input
  $form = Request::allPost();

  $db->where('email', $form['email']);
  $db->getOne(TABLE[1]);

  // Exit if token not found
  $db->count === 0 &&
    Response::message([
      'error' => [
        'message' => 'User not found!',
      ],
    ]);

  // Hash password
  $user['password'] = password_hash($form['password'], PASSWORD_DEFAULT);

  // Reset token
  $user['token'] = bin2hex(random_bytes(20));

  $db->where('email', $form['email']);
  $db->update(TABLE[1], $user, 1);

  !$db->getLastErrno()
    ? Response::message([
      'success' => [
        'message' => 'Password has been set!',
      ],
    ])
    : Response::message(['error' => ['message' => $db->getLastError()]]);
}
