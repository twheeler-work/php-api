<?php

namespace Http;

use Utilities\Encode;
use Http\Response;
use Carbon\Carbon;

class JWT
{
  function __construct()
  {
    // get the local secret key
    $this->secret = getenv('SECRET');
  }

  /** ----------------------------
   ** Create JWT token
   * -----------------------------
   * Create a token with encoded
   *  values to frontend.
   *
   * @param array data
   * @return string
   */
  public function createJWT(array $data = null)
  {
    // Create the token header
    $header = json_encode([
      'typ' => 'JWT',
      'alg' => 'HS256',
    ]);

    // Create the token payload
    if ($data) {
      $data['exp'] = Carbon::now()->addHour(1);
      $payload = json_encode($data);
    } elseif (Session::get('API_User')) {
      $payload = json_encode([
        'exp' => Carbon::now()->addHour(1),
        'restrictedTo' => Session::get('API_User'),
      ]);
    } else {
      $payload = json_encode([
        'exp' => Carbon::now()->addHour(1),
        'loggedIn' => false,
      ]);
    }

    // var_dump($payload);
    // die();

    // Encode Header
    $base64UrlHeader = Encode::base64Url($header);

    // Encode Payload
    $base64UrlPayload = Encode::base64Url($payload);

    // Create Signature Hash
    $signature = hash_hmac(
      'sha256',
      $base64UrlHeader . "." . $base64UrlPayload,
      $base64UrlPayload . $this->secret,
      true
    );

    // Encode Signature to Base64Url String
    $base64UrlSignature = Encode::base64Url($signature);

    // Create JWT
    $jwt =
      $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return $jwt;
  }

  /** ----------------------------
   ** Verify JWT Token
   * -----------------------------
   * Verify token and return success
   *  response OR return payload info.
   * - Restrict access to specific brand
   *
   * @param string token
   * @param string restricted
   * @return mixed
   */
  public function verifyJWT($jwt, $restricted = null)
  {
    // split the token
    $tokenParts = explode('.', $jwt);
    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signatureProvided = $tokenParts[2];

    // Set as array for return
    $payloadRaw = $payload;

    if ($restricted) {
      $raw = json_decode($payloadRaw, true);
      if (
        !empty($raw['restrictedTo']) &&
        !in_array($restricted, $raw['restrictedTo'])
      ) {
        Response::message(
          [
            'error' => [
              'message' =>
                'You do not have correct permissions to access this resource!',
            ],
          ],
          403
        );
      }
    }

    // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
    $expiration = date('Y-m-d H:i:s', strtotime(json_decode($payload)->exp));
    $tokenExpired = Carbon::now()->diffInSeconds($expiration, false) < 0;

    // build a signature based on the header and payload using the secret
    $base64UrlHeader = Encode::base64Url($header);
    $base64UrlPayload = Encode::base64Url($payload);

    $signature = hash_hmac(
      'sha256',
      $base64UrlHeader . "." . $base64UrlPayload,
      $base64UrlPayload . $this->secret,
      true
    );
    $base64UrlSignature = Encode::base64Url($signature);

    // verify it matches the signature provided in the token
    $signatureValid = $base64UrlSignature === $signatureProvided;

    if (!$tokenExpired && $signatureValid) {
      return json_decode($payloadRaw, true);
    } else {
      Response::message(
        ['error' => ['message' => 'Session has expired!']],
        403
      );
    }
  }
}