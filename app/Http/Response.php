<?php

namespace Http;

class Response
{
  /** ----------------------------------
   ** Exit and return jsonified message
   * -----------------------------------
   * @param {mixed} $status
   * @param {bool} $exit after echo (default)
   * @return json array
   */
  public static function message($status, $header = 200, $exit = true)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
      http_response_code($header);
      echo json_encode($status);
      $exit && exit();
    }
  }
}
