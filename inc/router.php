<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;

  if ($action === 'register') {
    require_once '../classes/authentication.php';
    $authentication = new Authentication();
    $authentication->register($_POST);
  } else if ($action === 'login') {
    require_once '../classes/authentication.php';
    $authentication = new Authentication();
    $authentication->login($_POST);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($_GET['action'] === 'logout') {
    require_once '../classes/authentication.php';
    $authentication = new Authentication();
    $authentication->logout();
  }
}
