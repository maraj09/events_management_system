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
  } else if ($action === 'add-event') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->store($_POST, $_FILES);
  } else if ($action === 'update-event') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->update($_GET['id'], $_POST, $_FILES);
  } else if ($action === 'delete-event') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->delete($_POST);
  } else if ($action === 'book-event') {
    require_once realpath(__DIR__) . '/../classes/EventBooking.php';
    $event = new EventBooking();
    $event->book($_POST);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($_GET['action'] === 'logout') {
    require_once realpath(__DIR__) . '/../classes/Authentication.php';
    $authentication = new Authentication();
    $authentication->logout();
  } else if ($_GET['action'] === 'load-events') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->index($_GET);
  } else if ($_GET['action'] === 'edit-event') {
    require_once realpath(__DIR__) . '/../classes/Event.php';
    $event = new Event();
    $event->edit($_GET);
  }
}
