<?php require_once __DIR__ . "/../app/_config/index.php";

use Http\Auth;
use Http\Request;
use Http\Response;
use Mail\Email;

// Verify and get agent ID
$auth = Auth::verifyToken();

// Handle form submit
if (!empty($_POST)) {
  $form = Request::allPost();

  // Set from token
  $form['agentID'] = $auth['agentID'];
  $form['brand'] = $auth['brand'] === null ? "NA" : $auth['brand'];

  $db->where("brand", $form['brand']);
  $db->where('address', $form['address']);
  $db->getOne(TABLE[0]);

  $db->count > 0 &&
    Response::message(
      [
        'error' => ['message' => 'This address has already been submitted!'],
      ],
      406
    );
  // Format date & time
  isset($form['date']) &&
    ($form['date'] = date('Y-m-d', strtotime($form['date'])));
  isset($form['time']) &&
    ($form['time'] = date('g:i a', strtotime($form['time'])));

  $db->insert(TABLE[0], $form);

  if (!$db->getLastErrno()) {
    if ($form['status'] === 'Do Not Knock') {
      // Get agent info
      $db->where('agentID', $form['agentID']);
      $agent = $db->getOne(TABLE[1]);

      if ($db->count > 0) {
        $values = [
          'name' => $agent['name'],
          'brand' => $agent['brand'],
          'state' => $agent['state'],
          'submitted' => date('m-d-Y h:i:s', strtotime('now')),
        ];
        (new Email())->mailLead($values);
      } else {
        Response::message(
          [
            'success' => [
              'message' => 'Lead submitted, but email could not be sent!',
            ],
          ],
          201
        );
      }
    }
    Response::message(['success' => ['message' => 'Lead submitted!']], 201);
  } else {
    Response::message(['error' => ['message' => $db->getLastError()]], 406);
  }

  $db->disconnect();
}
