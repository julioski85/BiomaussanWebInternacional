<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (!is_post() || !verify_csrf($_POST['csrf_token'] ?? null)) {
    redirect('dashboard.php');
}

$mysqli = db();

if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    if ($id > 0) {
        $stmt = $mysqli->prepare('DELETE FROM submissions WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    redirect('dashboard.php');
}

$ids = $_POST['selected_ids'] ?? [];
$ids = array_filter(array_map('intval', is_array($ids) ? $ids : []), static fn ($id) => $id > 0);

if (count($ids) > 0 && isset($_POST['bulk_delete'])) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $mysqli->prepare("DELETE FROM submissions WHERE id IN ({$placeholders})");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $stmt->close();
}

redirect('dashboard.php');
