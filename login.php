<?php
require_once './layouts/header.php';
Session::redirectIfLoggedIn();
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-lg">
        <div class="card-body">
          <h3 class="text-center mb-4">Login</h3>
          <form action="login.php" method="post">
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>
          <p class="text-center mt-3">
            Don't have an account? <a href="register.php">Register</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
require_once './layouts/footer.php'
?>