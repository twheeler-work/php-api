<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Request;
use Http\Response;

// Verify new password
if ($_POST) {
  // Sanitize input
  $form = Request::allPost();

  $db->where('token', $form['token']);
  $user = $db->getOne(TABLE[1]);

  // Exit if token not found
  $db->count > 0
    ? Response::message([
      'success' => [
        'message' => 'Token found!',
        'email' => $user['email'],
      ],
    ])
    : Response::message(
      [
        'error' => [
          'message' => 'Token not found! Please try the link again.',
        ],
      ],
      406
    );
}
