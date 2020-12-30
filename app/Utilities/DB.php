<?php

namespace Utilities;

use MysqliDb;

class DB
{
  protected $db = DBDATA;
  protected $local = LOCAL;

  // Connection
  public $conn;

  function __construct()
  {
    $this->host = $this->db['DB']['host'];
    $this->username = $this->db['DB']['username'];
    $this->password = $this->db['DB']['password'];
    $this->db = $this->db['DB']['db'];
    $this->port = 3306;

    // Local ENV
    $this->localHost = 'localhost';
    $this->localUsername = 'root';
    $this->localPassword = '';
  }

  // get the database connection
  public function start()
  {
    if ($this->local === true) {
      $dbSQL = [
        //Staging DB
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'db' => $this->db,
        'port' => $this->port,
      ];

      // Call Staging  DB
      $db = new Mysqlidb($dbSQL);

      if (!$db) {
        die("Please try again later.");
      }
    } else {
      $dbSQL = [
        //Production DB
        'host' => $this->host,
        'username' => $this->username,
        'password' => $this->password,
        'db' => $this->db,
        'port' => $this->port,
      ];

      // Call Production  DB
      $db = new Mysqlidb($dbSQL);

      if (!$db) {
        die("Please try again later.");
      }
    }
    return $db;
  }
}
