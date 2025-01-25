<?php
include './inc/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Management System</title>
  <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-md">
      <a class="navbar-brand" href="index.php">Events Management System</a>
      <?php if (Session::get('login')) { ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mb-2 mb-lg-0 w-100">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="events.php">All Events</a>
            </li>
            <li class="nav-item dropdown ms-auto">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo Session::get('user_name') ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Add Events</a></li>
                <li><a class="dropdown-item" href="#">My Events</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="./inc/router.php?action=logout">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      <?php } ?>
    </div>
  </nav>