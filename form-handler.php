<?php

declare(strict_types=1);

require_once __DIR__ . '/admin/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$fullName = trim((string)($_POST['name'] ?? ''));
$company = trim((string)($_POST['company'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$territory = trim((string)($_POST['territory'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($fullName === '' || $company === '' || $email === '' || $territory === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please complete all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (mb_strlen($message) > 3000) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Message is too long.']);
    exit;
}

$mysqli = db();
$stmt = $mysqli->prepare('INSERT INTO submissions (full_name, company, email, phone, territory, message) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssss', $fullName, $company, $email, $phone, $territory, $message);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save your application right now. Please try again.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Thank you. Your application was sent successfully.']);
