<?php
// required headers
header('Access-Control-Allow-Origin:' . CORS_URL);
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header(
  'Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Access'
);
header("Content-Type: application/json; charset=UTF-8");
