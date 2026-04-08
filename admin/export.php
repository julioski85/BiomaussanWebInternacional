<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$mysqli = db();
$search = trim((string)($_GET['q'] ?? ''));

$sql = 'SELECT id, full_name, company, email, phone, territory, message, created_at FROM submissions';
$params = [];
$types = '';

if ($search !== '') {
    $sql .= ' WHERE (full_name LIKE ? OR company LIKE ? OR email LIKE ? OR phone LIKE ? OR territory LIKE ? OR message LIKE ?)';
    $needle = '%' . $search . '%';
    $params = array_fill(0, 6, $needle);
    $types = str_repeat('s', 6);
}

$sql .= ' ORDER BY created_at DESC';
$stmt = $mysqli->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="submissions_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'wb');
fputcsv($output, ['ID', 'Full Name', 'Company', 'Email', 'Phone / WhatsApp', 'Country / Territory', 'Message', 'Created date']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['full_name'],
        $row['company'],
        $row['email'],
        $row['phone'],
        $row['territory'],
        $row['message'],
        $row['created_at'],
    ]);
}

fclose($output);
$stmt->close();
exit;
