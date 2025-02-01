<?php

require_once realpath(__DIR__) . '/../database/database.php';
require_once realpath(__DIR__) . '/../inc/session.php';
require_once realpath(__DIR__) . '/Helper.php';

class Event
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function index($data)
  {
    Helper::validateUserToken();

    $page = $data['page'];
    $perPage = $data['perPage'];
    $offset = ($page - 1) * $perPage;
    $filter = $data['filter'] ?? 'created_at';
    $search = $data['search'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;


    $sql = "SELECT * FROM events";

    if ($filter === 'only_mine' && $userId) {
      $sql .= " WHERE user_id = $userId";
    }

    if (!empty($search)) {
      if (strpos($sql, 'WHERE') !== false) {
        $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR event_date LIKE '%$search%')";
      } else {
        $sql .= " WHERE (name LIKE '%$search%' OR description LIKE '%$search%' OR event_date LIKE '%$search%')";
      }
    }

    if ($filter === 'created_at') {
      $sql .= " ORDER BY created_at DESC";
    } elseif ($filter === 'event_date') {
      $sql .= " ORDER BY event_date DESC";
    } elseif ($filter === 'name') {
      $sql .= " ORDER BY name ASC";
    }

    $sql .= " LIMIT $perPage OFFSET $offset";

    try {
      $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
      $totalEvents = $this->countTotalEvents();

      echo json_encode([
        'status' => 'success',
        'data' => $result,
        'totalEvents' => $totalEvents,
        'perPage' => $perPage,
        'currentPage' => $page,
      ]);
    } catch (PDOException $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
      ]);
    }
  }


  public function countTotalEvents()
  {
    $sql = "SELECT COUNT(*) as total FROM events";
    $result = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
  }

  public function validateEvent($data, $file)
  {
    $errors = [];

    Helper::validateCSRF($data['csrf_token']);

    Helper::validateUserToken();

    if (empty($data['name'])) {
      $errors['name'] = "Event name is required.";
    }

    if (empty($data['description'])) {
      $errors['description'] = "Event description is required.";
    } elseif (strlen($data['description']) < 10) {
      $errors['description'] = "Description must be at least 10 characters long.";
    }

    if (empty($data['event_date'])) {
      $errors['event_date'] = "Event date and time is required.";
    } elseif (!strtotime($data['event_date'])) {
      $errors['event_date'] = "Invalid date and time format.";
    }

    if (empty($data['user_limit'])) {
      $errors['user_limit'] = "Registration limit is required.";
    } elseif (!is_numeric($data['user_limit']) || $data['user_limit'] < 1) {
      $errors['user_limit'] = "Registration limit must be a number greater than 0.";
    }

    if (!empty($file['image']['name'])) {
      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
      $fileExtension = pathinfo($file['image']['name'], PATHINFO_EXTENSION);
      if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        $errors['image'] = "Image must be in JPG, JPEG, PNG, or GIF format.";
      }
    }

    return $errors;
  }

  public function store($data, $file)
  {
    $validationErrors = $this->validateEvent($data, $file);

    if (!empty($validationErrors)) {
      echo json_encode(['status' => 'error', 'errors' => $validationErrors]);
      exit;
    }

    $imagePath = $this->handleImageUpload($file['image'] ?? null);

    $userId = $_SESSION['user_id'] ?? null;

    $query = "INSERT INTO events (name, description, image, event_date, `user_limit`, user_id) VALUES (?, ?, ?, ?, ?, ?)";

    try {
      $this->db->query($query, [
        $data['name'],
        $data['description'],
        $imagePath,
        $data['event_date'],
        $data['user_limit'],
        $userId
      ], true);

      echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
      throw new Exception("Failed to store event: " . $e->getMessage());
    }
  }

  public function show($id)
  {
    Helper::validateUserToken();

    $userId = $_SESSION['user_id'] ?? null;

    $query = "SELECT role FROM users WHERE id = ?";
    $userRole = $this->db->query($query, [$userId])->fetchColumn();

    $event = $this->db->query("SELECT * FROM events WHERE id = $id")->fetch();
    $totalLimit = $event['user_limit'];

    $totalBooked = $this->db->query("SELECT SUM(quantity) as total FROM event_bookings WHERE event_id = ?", [$event['id']])->fetch();
    $totalBooked = $totalBooked['total'] ?? 0;

    $availableSeat = $totalLimit - $totalBooked;

    return ['event' => $event, 'availableSeat' => $availableSeat, 'userRole' => $userRole];
  }



  public function edit($data)
  {

    Helper::validateUserToken();

    $eventId = $data['id'];
    $event = $this->db->query("SELECT * FROM events WHERE id = $eventId")->fetch();

    if ($event) {
      echo json_encode(['status' => 'success', 'event' => $event]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Event not found!']);
    }
  }

  public function update($eventId, $data, $file)
  {
    $validationErrors = $this->validateEvent($data, $file);

    if (!empty($validationErrors)) {
      echo json_encode(['status' => 'error', 'errors' => $validationErrors]);
      exit;
    }

    $existingEvent = $this->db->query("SELECT * FROM events WHERE id = ?", [$eventId])->fetch();
    if (!$existingEvent) {
      echo json_encode(['status' => 'error', 'message' => 'Event not found']);
      exit;
    }

    $userId = $_SESSION['user_id'] ?? null;

    if ($existingEvent['user_id'] != $userId && Session::get('user_role') == 'user') {
      echo json_encode(['status' => 'error', 'message' => 'You cant modify other\'s event!']);
      exit;
    }

    $imagePath = $existingEvent['image'];
    if (!empty($file['image']) && $file['image']['error'] === UPLOAD_ERR_OK) {
      if ($imagePath && file_exists(realpath(__DIR__) . '/..' . $imagePath)) {
        unlink(realpath(__DIR__) . '/..' . $imagePath);
      }

      $imagePath = $this->handleImageUpload($file['image']);
    }

    $query = "UPDATE events 
              SET name = ?, description = ?, image = ?, event_date = ?, `user_limit` = ?
              WHERE id = ?";

    try {
      $this->db->query($query, [
        $data['name'],
        $data['description'],
        $imagePath,
        $data['event_date'],
        $data['user_limit'],
        $eventId,
      ]);

      echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
      throw new Exception("Failed to update event: " . $e->getMessage());
    }
  }

  private function handleImageUpload($file)
  {
    if (empty($file) || empty($file['name'])) {
      return null;
    }

    $imageName = time() . '_' . basename($file['name']);
    $uploadDir = realpath(__DIR__) . '/../uploads/events/';

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
      throw new Exception("Failed to create the upload directory.");
    }

    $uploadPath = $uploadDir . $imageName;
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
      throw new Exception("Failed to upload the image.");
    }

    return '/uploads/events/' . $imageName;
  }

  public function delete($data)
  {
    $eventId = $data['id'];
    Helper::validateUserToken();
    $userId = $_SESSION['user_id'] ?? null;

    $existingEvent = $this->db->query("SELECT * FROM events WHERE id = ?", [$eventId])->fetch();
    if ($existingEvent['user_id'] != $userId && Session::get('user_role') == 'user') {
      echo json_encode(['status' => 'error', 'message' => 'You cant modify other\'s event!']);
      exit;
    }

    $imagePath = $existingEvent['image'];
    if ($imagePath && file_exists(realpath(__DIR__) . '/..' . $imagePath)) {
      unlink(realpath(__DIR__) . '/..' . $imagePath);
    }

    $query = "DELETE FROM events WHERE id = ?";
    $this->db->query($query, [$eventId]);

    echo json_encode(['status' => 'success']);
  }

  public function showEventApi($id)
  {
    header('Content-Type: application/json');

    Helper::validateBearerToken();

    $event = $this->db->query("SELECT * FROM events WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    $totalLimit = $event['user_limit'];

    $totalBooked = $this->db->query("SELECT SUM(quantity) as total FROM event_bookings WHERE event_id = ?", [$event['id']])->fetch();
    $totalBooked = $totalBooked['total'] ?? 0;

    $availableSeat = $totalLimit - $totalBooked;

    echo json_encode(['status' => 'success', 'event' => $event, 'availableSeat' => $availableSeat]);
  }
}
