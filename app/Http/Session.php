<?php

namespace Http;

use Http\Request;
use Exception;

class Session
{
  protected $requireLogin;

  /** ---------------------------------
   ** Initialize session with login
   *  ---------------------------------
   * @param bool login default true
   */
  function __construct($login = true)
  {
    $this->start();
    $this->csrf_token();
    $this->requireLogin = $login;
  }

  /** ---------------------------
   ** Start session
   * ----------------------------
   * Start new session with
   *  timeout counter.
   */
  private function start()
  {
    !isset($_SESSION) && session_start();
  }

  /** ---------------------------
   ** Create session
   * ----------------------------
   * Create session from value
   * @param string $session
   * @param string $value
   * @param bool $override replace value
   */
  public static function set($session, $value, $override = false)
  {
    try {
      if (!$override && isset($_SESSION[$session])) {
        is_array($value) && ($value = $value[0]);
        array_push($_SESSION[$session], $value);
      } else {
        $_SESSION[$session] = $value;
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e->getMessage()]);
    }
  }

  /** ---------------------------
   ** Get session
   * ----------------------------
   * Get requested session
   * Optional clear after use
   * @param string $session
   * @param bool $clear get and clear
   * @return mixed array OR string
   */
  public static function get($session, $clear = null)
  {
    try {
      if (isset($_SESSION) && isset($_SESSION[$session])) {
        $return = $_SESSION[$session];
        $clear !== null && self::clear($session);
        return $return;
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }

  /** ---------------------------
   ** Clear session data
   * ----------------------------
   * - Destroy session
   */
  public static function clear($session = null)
  {
    try {
      if ($session === null) {
        if (isset($_SESSION)) {
          unset($_SESSION['errors']);
          unset($_SESSION['success']);
          unset($_SESSION['info']);
          unset($_SESSION['warnings']);
        }
      } else {
        if (isset($_SESSION)) {
          unset($_SESSION[$session]);
        }
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }

  /** ---------------------------
   ** Check for user session
   * ----------------------------
   * Redirect to login if session
   *  not set.
   * @param string $session
   */
  public static function active($session)
  {
    try {
      if (isset($_SESSION) && isset($_SESSION[$session])) {
        return $_SESSION[$session];
      } else {
        request::redirect('login');
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }

  /** ---------------------------
   ** Logout current user
   * ----------------------------
   * - Destroy session
   * - Redirect to login page
   *
   * @param array $status set status message
   * @param bool $setUri capture URI for continue
   */
  public static function logout(array $status = null, bool $setUri = false)
  {
    try {
      if (isset($_SESSION)) {
        unset($_SESSION);
        session_destroy();

        $setUri && (new Session())->set('uri', Request::getURI());
        $status && (new Session())->set('status', $status);
        Request::redirect('login');
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }

  /** -----------------------------
   ** Logout Unauthorized Session
   * ------------------------------
   * - Destroy session
   * - Redirect to login page
   *
   * @param array $status set status message
   */
  public static function unauthorized(array $status = null)
  {
    try {
      if (isset($_SESSION)) {
        unset($_SESSION);
        session_destroy();

        (new Session())->set('status', $status);
        Request::redirect('login', 401);
      }
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }

  /** ---------------------------
   ** Set CSRF Token
   * -----------------------------
   * Set token if it doesn't
   *  exist, get token.
   * @return string token
   */
  private function csrf_token()
  {
    try {
      if (empty(self::get('auth_token'))) {
        self::set('auth_token', bin2hex(random_bytes(20)));
      }
      return self::get('auth_token');
    } catch (Exception $e) {
      self::set('status', ['errors' => $e]);
    }
  }
}
