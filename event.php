<?php
require_once realpath(__DIR__) . '/layouts/header.php';
require_once realpath(__DIR__) . '/classes/Event.php';
Session::restrictAccess();
$event = new Event();
$data = $event->show($_GET['id']);
$event = $data['event'];
?>
<div id="alert-container" class="container py-4"></div>

<div class="container my-5">
  <?php if ($event): ?>
    <div class="card shadow-lg">
      <div class="row g-0">
        <?php if (!empty($event['image'])): ?>
          <div class="col-md-6">
            <img src="<?= '.' . htmlspecialchars($event['image']) ?>" class="img-fluid rounded-start" alt="Event Image">
          </div>
        <?php endif; ?>
        <div class="<?= empty($event['image']) ? 'col-md-12' : 'col-md-6' ?>">
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
            <p class="text-muted"><strong>Event Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <p><strong>Total Seat:</strong> <?= htmlspecialchars($event['user_limit']) ?></p>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#bookingModal">
              Book Now!
            </button>
            <span class="text-danger ms-3">Available Seats: <strong id="availableSeatCount"><?= $data['availableSeat'] ?></strong></span>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger text-center">Event not found.</div>
  <?php endif; ?>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalLabel">Book Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="bookingForm">
          <input type="hidden" id="eventId" name="event_id" value="<?= $event['id'] ?>">
          <input type="hidden" name="action" value="book-event">
          <input type="hidden" name="csrf_token" value="<?= Session::get('csrf_token'); ?>">
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="1">
            <div class="invalid-feedback">Quantity must be at least 1!</div>
          </div>

          <button type="submit" class="btn btn-primary">Confirm Booking</button>
        </form>
      </div>
    </div>
  </div>
</div>


<?php
require_once realpath(__DIR__) . '/layouts/footer.php';
?>