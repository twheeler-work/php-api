<?php
namespace Http;

class Headers
{
  static function get()
  {
    // required headers
    header("Access-Control-Allow-Origin: " . FRONTEND_URL);
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header(
      'Access-Control-Allow-Headers: Authorization, Origin, Content-Type, Access'
    );
    header("Content-Type: application/json; charset=UTF-8");
  }
  static function post()
  {
    // required headers
    header("Access-Control-Allow-Origin: " . FRONTEND_URL);
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header(
      'Access-Control-Allow-Headers: Authorization, Origin, Content-Type, Access'
    );
    header("Content-Type: application/json; charset=UTF-8");
  }
  static function delete()
  {
    // required headers
    header("Access-Control-Allow-Origin: " . FRONTEND_URL);
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: DELETE, OPTIONS');
    header(
      'Access-Control-Allow-Headers: Authorization, Origin, Content-Type, Access'
    );
    header("Content-Type: application/json; charset=UTF-8");
  }
}