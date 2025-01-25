<?php

class Session
{
  public static function start()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function destroy()
  {
    self::start();
    session_unset();
    session_destroy();
  }

  public static function set($key, $val)
  {
    self::start();
    $_SESSION[$key] = $val;
  }

  public static function get($key)
  {
    self::start();
    return $_SESSION[$key] ?? false;
  }


  public static function redirectIfLoggedIn()
  {
    self::start();
    if (self::get("login") === true) {
      header("Location: index.php");
      exit;
    }
  }

  public static function restrictAccess()
  {
    self::start();
    if (self::get("login") !== true) {
      self::destroy();
      header("Location: login.php");
      exit;
    }
  }
}
