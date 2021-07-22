<?php

namespace Http;

use Http\Session;
use FFI\Exception;

class Request
{
  public function handleRequest()
  {
    if ($_POST || $_GET) {
      return true;
    } else {
      return false;
    }
  }

  /** ----------------------------
   ** Redirect browser
   * -----------------------------
   * @return string $query
   */
  public static function redirect($page, $code = null)
  {
    try {
      $page === "/" && ($page = "");
      $code
        ? header("Location: /$page", true, $code)
        : header("Location: /$page");
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Get GET query
   * -----------------------------
   * Get & clean query
   * @return string $query
   */
  public static function get($query)
  {
    try {
      if (isset($_GET[$query])) {
        return htmlspecialchars($_GET[$query]);
      } else {
        return '';
      }
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Get JSON query
   * -----------------------------
   * Get & clean query
   * @return object $query
   */
  public static function json()
  {
    try {
      $json = json_decode(file_get_contents("php://input"), true);
      if (!empty($json)) {
        $form = [];
        foreach ($json as $name => $value) {
          if (!is_array($value)) {
            $form[htmlspecialchars($name)] = htmlspecialchars($value);
          } else {
            foreach ($value as $n => $v) {
              if (!is_array($v)) {
                $form[htmlspecialchars($name)][$n] = htmlspecialchars($v);
              } else {
                foreach ($v as $n2 => $v2) {
                  $form[htmlspecialchars($name)][$n][$n2] = htmlspecialchars(
                    $v2
                  );
                }
              }
            }
          }
        }
        return $form;
      } else {
        return null;
      }
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Get POST query
   * -----------------------------
   * Get & clean query
   * @return string $query
   */
  public static function post($query)
  {
    if (isset($_POST[$query])) {
      return htmlspecialchars($_POST[$query]);
    } else {
      return '';
    }
  }

  /** ----------------------------
   ** Get All GET queries
   * -----------------------------
   * Get & clean GET queries
   * @return array $form
   */
  public static function allGet()
  {
    try {
      $form = [];
      foreach ($_GET as $name => $value) {
        $form[htmlspecialchars($name)] = htmlspecialchars($value);
      }
      return $form;
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Get All POST queries
   * -----------------------------
   * Get & clean POST queries
   * @return array $form
   */
  public static function allPost()
  {
    try {
      $form = [];
      foreach ($_POST as $name => $value) {
        $form[htmlspecialchars($name)] = htmlspecialchars($value);
      }
      return $form;
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Get API parameters
   * -----------------------------
   * Get & clean GET queries
   * @return array $form
   */
  public static function getAPI($query)
  {
    try {
      if (isset($_GET[$query])) {
        $params = explode("/", $_GET[$query]);
        foreach ($params as $param) {
          !empty($param) &&
            ($return[htmlspecialchars($param)] = htmlspecialchars($param));
        }
        return isset($return) ? $return : null;
      } else {
        return '';
      }
    } catch (Exception $e) {
      die($e);
    }
  }

  /** ----------------------------
   ** Return HTTP_HOST
   * -----------------------------
   * @return string host
   */
  public static function getHost()
  {
    if ($_SERVER['HTTP_HOST']) {
      return urldecode(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH));
    } else {
      return null;
    }
  }

  /** ----------------------------
   ** Return REQUEST_URI
   * -----------------------------
   * @return string uri
   */
  public static function getURI()
  {
    if (isset($_SERVER['REQUEST_URI'])) {
      return urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    } else {
      return '/';
    }
  }

  /** ----------------------------
   *? Return HTTP_X_AUTH_TOKEN
   * -----------------------------
   * @return string token
   */
  public static function getXToken()
  {
    if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
      return urldecode(parse_url($_SERVER['HTTP_X_AUTH_TOKEN'], PHP_URL_PATH));
    } else {
      return null;
    }
  }

  /** ----------------------------
   ** Return HTTP_REFERER
   * -----------------------------
   * @return string referer
   */
  public static function getReferer()
  {
    if (isset($_SERVER['HTTP_REFERER'])) {
      return urldecode(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    } else {
      return null;
    }
  }

  /** ----------------------------
   ** Return DOCUMENT_ROOT
   * -----------------------------
   * @return string root
   */
  public static function getRoot()
  {
    if (isset($_SERVER['DOCUMENT_ROOT'])) {
      return urldecode(parse_url($_SERVER['DOCUMENT_ROOT'], PHP_URL_PATH));
    } else {
      return null;
    }
  }

  /** ----------------------------
   ** Set CORS Header
   * -----------------------------
   * OPTIONS:
   * get -
   * post -
   * delete -
   *
   * @param string $type set predefined cors
   * @return cors header
   */
  public static function setHeader(string $type)
  {
    if (isset($type)) {
      // Defaults
      header('Access-Control-Allow-Origin:' . CORS_URL);
      header("Access-Control-Allow-Credentials: true");
      header("Content-Type: application/json; charset=UTF-8");
      header(
        'Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Access'
      );
    } else {
      header('Access-Control-Allow-Origin: *');
      header("Content-Type: application/json; charset=UTF-8");
    }

    if ($type === 'get') {
      header('Access-Control-Allow-Methods: GET, OPTIONS');
    }

    if ($type === 'post') {
      header('Access-Control-Allow-Methods: POST, OPTIONS');
    }

    if ($type === 'delete') {
      header('Access-Control-Allow-Methods: DELETE, OPTIONS');
    }
  }
}