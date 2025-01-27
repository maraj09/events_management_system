<?php
require_once realpath(__DIR__) . '/layouts/header.php';
require_once realpath(__DIR__) . '/classes/Event.php';
Session::restrictAccess();
?>

<div id="alert-container" class="container py-4"></div>

<div class="container">
  <div class="d-flex align-items-center mb-4">
    <h3>My Events</h3>
    <div class="ms-auto col-3">
      <input type="text" class="form-control bg-light" placeholder="Search">
    </div>
    <div class="ms-3">
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#eventModal">+ New Event</button>
    </div>
  </div>
  <div class="container">
    <div class="row event-container">

    </div>
    <nav>
      <ul class="pagination justify-content-center mt-4" id="pagination">

      </ul>
    </nav>
  </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Create Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="eventForm" method="POST" action="./inc/router.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_event">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
          <div class="mb-3">
            <label for="eventName" class="form-label">Event Name</label>
            <input type="text" class="form-control" id="eventName" name="name">
            <div class="invalid-feedback">Please provide a valid event name.</div>
          </div>
          <div class="mb-3">
            <label for="eventDescription" class="form-label">Description</label>
            <textarea class="form-control" id="eventDescription" name="description" rows="4"></textarea>
            <div class="invalid-feedback">Please provide a valid description.</div>
          </div>
          <div class="mb-3">
            <label for="eventImage" class="form-label">Event Image (optional)</label>
            <input type="file" class="form-control" id="eventImage" name="image">
            <div class="invalid-feedback">Please upload a valid image (JPEG, PNG, JPG, or GIF).</div>
          </div>
          <div class="mb-3">
            <label for="eventDate" class="form-label">Event Date and Time</label>
            <input type="datetime-local" class="form-control" id="eventDate" name="event_date">
            <div class="invalid-feedback">Please select a valid date and time.</div>
          </div>
          <div class="mb-3">
            <label for="userLimit" class="form-label">Event Limit (Number of Participants)</label>
            <input type="number" class="form-control" id="userLimit" name="user_limit" min="1">
            <div class="invalid-feedback">Please provide a valid number greater than 0.</div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Event</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php
require_once realpath(__DIR__) . '/layouts/footer.php';
?>