<?php
require_once realpath(__DIR__) . '/../database/database.php';
require_once realpath(__DIR__) . '/../inc/session.php';
require_once realpath(__DIR__) . '/Helper.php';

class EventBooking
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function book($data)
  {
    Helper::validateCSRF($data['csrf_token']);

    Helper::validateUserToken();

    $eventId = $data['event_id'];
    $quantity = $data['quantity'];
    $userId = Session::get('user_id');

    if ($quantity < 1) {
      echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1']);
      exit;
    }

    $event = $this->db->query("SELECT user_limit FROM events WHERE id = ?", [$eventId])->fetch();
    if (!$event) {
      echo json_encode(['status' => 'error', 'message' => 'Event not found']);
      exit;
    }
    $totalLimit = $event['user_limit'];

    $totalBooked = $this->db->query("SELECT SUM(quantity) as total FROM event_bookings WHERE event_id = ?", [$eventId])->fetch();
    $totalBooked = $totalBooked['total'] ?? 0;

    if ($totalBooked + $quantity > $totalLimit) {
      echo json_encode(['status' => 'error', 'message' => 'Booking limit reached for this event']);
      exit;
    }

    try {
      $this->db->query(
        "INSERT INTO event_bookings (event_id, user_id, quantity) VALUES (?, ?, ?)",
        [$eventId, $userId, $quantity]
      );

      $availableSeat = $totalLimit - $totalBooked - $quantity;

      echo json_encode(['status' => 'success', 'message' => 'Event booked successfully!', 'availableSeat' => $availableSeat]);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function generateEventReport($data)
  {
    Helper::validateUserToken();

    $userId = $_SESSION['user_id'] ?? null;

    $query = "SELECT role FROM users WHERE id = ?";
    $userRole = $this->db->query($query, [$userId])->fetchColumn();

    if ($userRole !== 'admin') {
      throw new Exception("User not admin.");
    }

    $eventId = $data['id'];
    $fileName = "event_report_" . $eventId . ".csv";

    $sql = "SELECT users.id AS user_id, users.name, users.email, SUM(event_bookings.quantity) AS total_quantity
            FROM event_bookings
            JOIN users ON event_bookings.user_id = users.id
            WHERE event_bookings.event_id = ?
            GROUP BY event_bookings.user_id";

    $stmt = $this->db->query($sql, [$eventId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    $output = fopen('php://output', 'w');

    fputcsv($output, ['User ID', 'Name', 'Email', 'Total Quantity']);

    foreach ($bookings as $row) {
      fputcsv($output, $row);
    }

    fclose($output);
    exit;
  }
}
