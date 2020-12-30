<?php

namespace Utilities;

class Encode
{
  // PHP has no base64UrlEncode function, so let's define one that
  // does some magic by replacing + with -, / with _ and = with ''.
  // This way we can pass the string within URLs without
  // any URL encoding.
  public static function base64Url($text)
  {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
  }
}
