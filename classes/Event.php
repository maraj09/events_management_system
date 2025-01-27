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

    $sql = "SELECT * FROM events ORDER BY created_at DESC LIMIT $offset, $perPage";
    $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $totalEvents = $this->countTotalEvents();

    echo json_encode([
      'status' => 'success',
      'data' => $result,
      'totalEvents' => $totalEvents,
      'perPage' => $perPage,
      'currentPage' => $page,
    ]);
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
      $eventId = $this->db->query($query, [
        $data['name'],
        $data['description'],
        $imagePath,
        $data['event_date'],
        $data['user_limit'],
        $userId
      ], true);

      $event = $this->db->query("SELECT * FROM events WHERE id = $eventId")->fetch();

      echo json_encode(['status' => 'success', 'event' => $event]);
    } catch (Exception $e) {
      throw new Exception("Failed to store event: " . $e->getMessage());
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
}
