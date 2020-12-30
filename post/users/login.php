<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\JWT;
use Http\Request;
use Http\Response;

if ($_POST) {
  // Sanitize input
  $form = Request::allPost();

  $db->where('email', $form['username']);
  $user = $db->getOne(TABLE[1]);

  // Exit if token not found
  $db->count === 0 &&
    Response::message([
      'error' => [
        'message' => 'Password or user is incorrect!',
        'testing' => 'User not found',
      ],
    ]);

  // Compare passwords
  if (password_verify($form['password'], $user['password'])) {
    // Return token
    $payload = [
      'name' => $user['fName'],
      'role' => $user['role'],
      'brand' => $user['brand'],
      'agentID' => $user['agentID'],
    ];
    $jwt = (new JWT())->createJWT($payload);
    Response::message([
      'success' => [
        'token' => $jwt,
        'brand' => $user['brand'],
      ],
    ]);
  } else {
    Response::message([
      'error' => [
        'message' => 'Password or user is incorrect!',
        'testing' => 'Password is incorrect',
      ],
    ]);
  }
}
