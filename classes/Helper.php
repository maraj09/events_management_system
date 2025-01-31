<?php
require_once realpath(__DIR__) . '/../inc/session.php';
require_once realpath(__DIR__) . '/../database/database.php';

class Helper
{
  public static function validateCSRF($token)
  {
    Session::start();
    if (empty($token) || $token !== $_SESSION['csrf_token']) {
      throw new Exception("CSRF Token Mismatch.");
    }
    return true;
  }

  public static function validateUserToken()
  {
    Session::start();
    $userId = $_SESSION['user_id'] ?? null;
    $userToken = $_SESSION['token'] ?? null;

    if (!$userId || !$userToken) {
      throw new Exception("User not authenticated.");
    }

    $db = new Database();
    $query = "SELECT token FROM users WHERE id = ?";
    $storedToken = $db->query($query, [$userId])->fetchColumn();

    if ($storedToken !== $userToken) {
      Session::destroy();
      header("Location: login.php");
    }
    return true;
  }
}
