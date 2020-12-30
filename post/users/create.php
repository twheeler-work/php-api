<?php require_once __DIR__ . '/../../app/_config/index.php';

use Http\Auth;
use Http\Request;
use Http\Response;
use Mail\Email;

// Check for valid token
$auth = Auth::verifyToken();

// Sanitize input
$form = Request::allPost();

// No agents allowed
$auth['role'] === 'agent' &&
  Response::message(
    [
      'error' => [
        'message' => 'You are not authorized to create users!',
      ],
    ],
    405
  );

$db->where('email', $form['email']);
$user = $db->getOne(TABLE[1]);

// Exit if user exists
$db->count > 0 &&
  Response::message(
    [
      'error' => [
        'message' => 'This user already exists!',
        'user' => $user['fname'] . ' ' . $user['lname'],
      ],
    ],
    406
  );

// Generate agent id
$id = strtolower($form['fname'][0] . $form['lname'][0] . $form['lname'][1]);
$form['agentID'] = $id . '-' . mt_rand(100000, 999999);

// Create token for email verification
$form['token'] = bin2hex(random_bytes(20));

$db->startTransaction();

if ($db->insert(TABLE[1], $form)) {
  if ((new Email())->newUser($form)) {
    $db->commit();

    $form['name'] = $form['fname'] . " " . $form['lname'];
    Response::message([
      'success' => [
        'message' => 'Verification email sent to ' . $form['email'],
        'values' => $form,
      ],
    ]);
  } else {
    $db->rollback();
  }
} else {
  Response::message(['error' => ['message' => $db->getLastError()]], 406);
}
