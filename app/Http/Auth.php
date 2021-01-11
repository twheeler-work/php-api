<?php

namespace Http;

use Http\JWT;

class Auth
{
  public static function getToken()
  {
    $jwt = new JWT();
    return $jwt->createJWT();
  }

  public static function verifyToken($status = null)
  {
    // Prevent preflight errors
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
      if (Request::getXToken()) {
        $token = Request::getXToken();
        return $status
          ? (new JWT())->verifyJWT($token, true)
          : (new JWT())->verifyJWT($token);
      } else {
        Response::message(
          [
            'error' => [
              'message' => 'You are not authorized to view this information!',
            ],
          ],
          401
        );
      }
    }
  }

  public static function generateSecret()
  {
    $secret = bin2hex(random_bytes(32));
    putenv("SECRET=$secret");
  }
}
