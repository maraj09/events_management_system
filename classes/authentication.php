<?php

require_once realpath(__DIR__) . '/../database/database.php';
require_once realpath(__DIR__) . '/../inc/session.php';

class Authentication
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function emailExists($email)
  {
    $query = "SELECT COUNT(*) FROM users WHERE email = ?";
    $stmt = $this->db->query($query, [$email]);
    return $stmt->fetchColumn() > 0; // Return true if email exists
  }

  public function login($data, $apiLogin = false)
  {
    $errors = [];

    if (empty($data['email'])) {
      $errors['email'] = "Email is required.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format.";
    }

    if (empty($data['password'])) {
      $errors['password'] = "Password is required.";
    }

    if (!empty($errors)) {
      Session::start();
      $_SESSION['errors'] = $errors;
      header("Location: ../login.php");
      exit;
    }

    $query = "SELECT * FROM users WHERE email = ?";
    $result = $this->db->query($query, [$data['email']]);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password'])) {
      Session::start();
      if ($apiLogin) {
        echo json_encode(['status' => 'success', 'email' => "Invalid email or password."]);
        exit;
      }
      $_SESSION['errors'] = ['email' => "Invalid email or password."];
      header("Location: ../login.php");
      exit;
    }

    $token = bin2hex(random_bytes(32));

    $updateQuery = "UPDATE users SET token = ? WHERE id = ?";
    $this->db->query($updateQuery, [$token, $user['id']]);

    if ($apiLogin) {
      echo json_encode(['status' => 'success', 'token' => $token]);
      exit;
    }

    Session::set("login", true);
    Session::set("user_id", $user['id']);
    Session::set("user_name", $user['name']);
    Session::set("user_email", $user['email']);
    Session::set("user_role", $user['role']);
    Session::set("token", $token);

    header("Location: ../index.php");
    exit;
  }

  public function register($data)
  {
    $errors = [];

    if (empty($data['name'])) {
      $errors['name'] = "Name is required.";
    }

    if (empty($data['email'])) {
      $errors['email'] = "Email is required.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format.";
    } elseif ($this->emailExists($data['email'])) {
      $errors['email'] = "Email is already in use.";
    }

    if (empty($data['password'])) {
      $errors['password'] = "Password is required.";
    }

    if (strlen($data['password']) < 8) {
      $errors['password'] = "Password must be at least 8 characters.";
    }

    if (empty($data['confirm_password'])) {
      $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($data['password'] !== $data['confirm_password']) {
      $errors['confirm_password'] = "Passwords do not match.";
    }

    if (!empty($errors)) {
      Session::start();
      $_SESSION['errors'] = $errors;
      header("Location: ../register.php", true, 303);
      exit;
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $token = bin2hex(random_bytes(32));

    $query = "INSERT INTO users (name, email, password, role, token) VALUES (?, ?, ?, ?, ?)";
    $userId = $this->db->query($query, [$data['name'], $data['email'], $hashedPassword, 'user', $token], true);

    Session::set("login", true);
    Session::set("user_id", $userId);
    Session::set("user_name", $data['name']);
    Session::set("user_email", $data['email']);
    Session::set("token", $token);

    header("Location: ../index.php", true, 303);
    exit;
  }

  public function logout()
  {
    Session::start();
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
      $query = "UPDATE users SET token = NULL WHERE id = ?";
      $this->db->query($query, [$userId]);
    }

    Session::destroy();
    header("Location: ../login.php");
    exit();
  }
}
