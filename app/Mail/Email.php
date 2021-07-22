<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use Http\Response;

class Email
{
  // SMTP Variables
  // --------------------------------------------------------------------
  private $fromEmail = 'thomas.wheeler@nrg.com'; // Sending address
  private $host = 'email-smtp.us-east-1.amazonaws.com'; // Specify main SMTP server
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

  /** -----------------------------------
   ** Demo email
   * ------------------------------------
   * This is a demo email function
   * - Configure your email
   * - Pass your values to the getEmail function
   *    + Applies your values to an html
   *      template
   * - Send email
   * @param array $values values to be sent
   * @return bool
   */
  public function demo(array $values)
  {
    try {
      //Recipients
      $this->mail->setFrom($this->fromEmail, 'Levelup Demo');
      $this->mail->addReplyTo('levelup@demo.com', 'Do Not Reply');
      $this->mail->addAddress($values['email']);

      // Content
      $this->mail->isHTML(true); // Set email format to HTML
      $this->mail->Subject = 'Levelup Demo';

      $body = $this->getEmail('demo', $values);

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
   * Insert an html template into an
   *  email body
   *
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