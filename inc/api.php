<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  if ($action === 'api-login') {
    require_once realpath(__DIR__) . '/../classes/Authentication.php';
    $authentication = new Authentication();
    $authentication->login($_POST, true);
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($_GET['action'] === 'api-event-details') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->showEventApi($_GET['id'], $_GET['token']);
  }
}
