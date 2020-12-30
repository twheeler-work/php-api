<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use Http\Response;

class Email
{
  // SMTP Variables
  // --------------------------------------------------------------------
  private $fromEmail = 'thomas.wheeler@nrg.com'; // This address must be verified with Amazon SES.
  private $host = 'email-smtp.us-east-1.amazonaws.com'; // Specify main and backup SMTP servers
  private $port = 587; // TCP port to connect to
  private $SMTPAuth = true; // Enable SMTP authentication
  private $SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
  private $username = 'AKIAWAP4UIXN44NTCA7A'; // SMTP username
  private $password = 'BDMBDpQd0RgN8w9BMzroOB3yvB4V8NjqZDjQhm8kI9QM'; // SMTP password
  private $SMTPDebug = 0; // Enable verbose debug output
  // --------------------------------------------------------------------

  function __construct()
  {
    $this->mail = new PHPMailer(true);
    $this->mail->isSMTP(); // Set mailer to use SMTP
    $this->mail->Host = $this->host;
    $this->mail->Port = $this->port;
    $this->mail->SMTPAuth = $this->SMTPAuth;
    $this->mail->Username = $this->username;
    $this->mail->Password = $this->password;
    $this->mail->SMTPSecure = $this->SMTPSecure;
    $this->mail->SMTPDebug = $this->SMTPDebug;
  }

  public function forgotPassword(array $values)
  {
    try {
      //Recipients
      $this->mail->setFrom($this->fromEmail, 'NRG Lead Portal');
      $this->mail->addReplyTo('no-reply@nrg.com', 'Do Not Reply');
      $this->mail->addAddress($values['email']);

      // Content
      $this->mail->isHTML(true); // Set email format to HTML
      $this->mail->Subject = 'Reset password request';

      $values['url'] =
        "https://" .
        $_SERVER['HTTP_HOST'] .
        '/set-password?id=' .
        $values['token'];

      $body = $this->getEmail('forgot-password', $values);

      $this->mail->Body = $body;
      $this->mail->send();
      return true;
    } catch (Exception $e) {
      echo $e;
    }
  }

  public function newUser(array $values)
  {
    try {
      //Recipients
      $this->mail->setFrom($this->fromEmail, 'NRG Lead Portal');
      $this->mail->addReplyTo('no-reply@nrg.com', 'Do Not Reply');
      $this->mail->addAddress($values['email']);

      // Content
      $this->mail->isHTML(true); // Set email format to HTML
      $this->mail->Subject = 'New user request';

      $values['url'] =
        "https://" . $_SERVER['HTTP_HOST'] . '/verify?id=' . $values['token'];

      $body = $this->getEmail('new-user', $values);

      $this->mail->Body = $body;
      $this->mail->send();
      return true;
    } catch (Exception $e) {
      echo $e;
    }
  }

  public function mailLead(array $values)
  {
    try {
      //Recipients
      $this->mail->setFrom($this->fromEmail, 'NRG Lead Portal');
      $this->mail->addReplyTo('no-reply@nrg.com', 'Do Not Reply');
      $this->mail->addAddress($values['email']);

      // Content
      $this->mail->isHTML(true); // Set email format to HTML
      $this->mail->Subject = 'New user request';

      $values['url'] =
        "https://" . $_SERVER['HTTP_HOST'] . '/verify?id=' . $values['token'];

      $body = $this->getEmail('new-user', $values);

      $this->mail->Body = $body;
      $this->mail->send();
      return true;
    } catch (Exception $e) {
      echo $e;
    }
  }

  /** ----------------------------------
   ** Return requested email template
   * -----------------------------------
   * @param string $templateName
   * @param array $values to insert into template
   * @return string template
   */
  private function getEmail($templateName, $values)
  {
    ob_start();
    include __DIR__ . "/../email-templates/${templateName}.php";
    $template = ob_get_clean();
    str_replace('"', "'", $template);
    return $template;
  }
}
