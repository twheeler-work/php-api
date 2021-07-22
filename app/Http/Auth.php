<?php

namespace Https;

use Http\JWT;
use Http\Response;
use Http\Session;

class Auth
{
  public static function getToken()
  {
    return (new JWT())->createJWT();
  }

  public static function getKey()
  {
    $key = getenv('KEY');
    return $key;
  }

  /** ---------------------------
   *? Validate user & password
   * -----------------------------
   * Checks for basic auth
   *
   * @return json
   */
  public static function verifyOATH()
  {
    // Prevent preflight errors
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
      // if (!VPN) {
      if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];

        if (isset(API_CREDS[$user]) && $pw === API_CREDS[$user]['pw']) {
          Session::set('API_User', API_CREDS[$user]['restrictedTo']);
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
      // }
    }
  }

  public static function verifyToken($restricted = null)
  {
    // Prevent preflight errors
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
      $token = Request::getXToken();
      if ($token && $token !== "null") {
        return $restricted
          ? (new JWT())->verifyJWT($token, $restricted)
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

  /** ---------------------------
   *? Generate secret and key
   * -----------------------------
   * Random generate secret and key
   *  to .env file without overriding
   *  existing vars.
   *
   * @return bool
   */
  public static function generateSecret()
  {
    $path = './../.env';
    $secret = bin2hex(random_bytes(32));
    $key = bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));

    $envs = [
      "SECRET" => $secret,
      "KEY" => $key,
    ];

    if (file_exists($path)) {
      $str = file_get_contents($path);
      $vars = explode("\n", $str);
      $update = "";

      foreach ($vars as $var) {
        $value = explode("=", $var);
        if (isset($envs[$value[0]])) {
          $update .= $value[0] . "=" . $envs[$value[0]] . "\n";
        } else {
          $update .= $var;
        }
      }
      file_put_contents($path, $update);
      return true;
    } else {
      return false;
    }
  }
}