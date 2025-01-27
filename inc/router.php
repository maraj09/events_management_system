<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;

  if ($action === 'register') {
    require_once realpath(__DIR__) . '/../classes/Authentication.php';
    $authentication = new Authentication();
    $authentication->register($_POST);
  } else if ($action === 'login') {
    require_once realpath(__DIR__) . '/../classes/Authentication.php';
    $authentication = new Authentication();
    $authentication->login($_POST);
  } else if ($action === 'add_event') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->store($_POST, $_FILES);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($_GET['action'] === 'logout') {
    require_once realpath(__DIR__) . '/../classes/Authentication.php';
    $authentication = new Authentication();
    $authentication->logout();
  }
  if ($_GET['action'] === 'load-events') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->index($_GET);
  }
}
