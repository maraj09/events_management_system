<?php
require_once realpath(__DIR__) . '/layouts/header.php';
Session::redirectIfLoggedIn();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-lg">
        <div class="card-body">
          <h3 class="text-center mb-4">Register</h3>
          <form action="./inc/router.php" method="post">
            <input type="hidden" name="action" value="register">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name">
              <?php if (isset($errors['name'])): ?>
                <small class="text-danger"><?= $errors['name'] ?></small>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
              <?php if (isset($errors['email'])): ?>
                <small class="text-danger"><?= $errors['email'] ?></small>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" placeholder="Enter your password" name="password">
              <?php if (isset($errors['password'])): ?>
                <small class="text-danger"><?= $errors['password'] ?></small>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirm_password" placeholder="Confirm your password" name="confirm_password">
              <?php if (isset($errors['confirm_password'])): ?>
                <small class="text-danger"><?= $errors['confirm_password'] ?></small>
              <?php endif; ?>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Register</button>
            </div>
          </form>
          <p class="text-center mt-3">
            Already have an account? <a href="login.php">Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once realpath(__DIR__) . '/layouts/footer.php'
?>