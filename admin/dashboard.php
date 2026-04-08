<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$mysqli = db();
$search = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$baseWhere = '1=1';
$params = [];
$types = '';

if ($search !== '') {
    $baseWhere = '(full_name LIKE ? OR company LIKE ? OR email LIKE ? OR phone LIKE ? OR territory LIKE ? OR message LIKE ?)';
    $needle = '%' . $search . '%';
    $params = array_fill(0, 6, $needle);
    $types = str_repeat('s', 6);
}

$countSql = "SELECT COUNT(*) AS total FROM submissions WHERE {$baseWhere}";
$countStmt = $mysqli->prepare($countSql);
if ($types !== '') {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();

$totalPages = max(1, (int)ceil($total / $limit));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$listSql = "SELECT id, full_name, company, email, phone, territory, message, created_at FROM submissions WHERE {$baseWhere} ORDER BY created_at DESC LIMIT ? OFFSET ?";
$listStmt = $mysqli->prepare($listSql);

if ($types !== '') {
    $listTypes = $types . 'ii';
    $listParams = array_merge($params, [$limit, $offset]);
    $listStmt->bind_param($listTypes, ...$listParams);
} else {
    $listStmt->bind_param('ii', $limit, $offset);
}

$listStmt->execute();
$rows = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$listStmt->close();

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Biomaussan International</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-app">
<header class="admin-header card">
    <div>
        <h1>Form Submissions</h1>
        <p>Total records: <?= $total ?></p>
    </div>
    <div class="header-actions">
        <a class="btn secondary" href="export.php<?= $search !== '' ? '?q=' . urlencode($search) : '' ?>">Export CSV</a>
        <a class="btn ghost" href="logout.php">Logout</a>
    </div>
</header>

<section class="card controls">
    <form method="get" class="search-form">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search name, company, email, territory...">
        <button class="btn primary" type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a class="btn ghost" href="dashboard.php">Clear</a>
        <?php endif; ?>
    </form>
</section>

<section class="card table-card">
    <?php if (count($rows) === 0): ?>
        <div class="empty-state">
            <h2>No submissions found</h2>
            <p>New form entries will appear here.</p>
        </div>
    <?php else: ?>
        <form method="post" action="delete.php" onsubmit="return confirm('Delete selected submissions? This cannot be undone.');">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country / Territory</th>
                        <th>Message</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="<?= (int)$row['id'] ?>"></td>
                            <td><?= (int)$row['id'] ?></td>
                            <td><?= e($row['full_name']) ?></td>
                            <td><?= e($row['company']) ?></td>
                            <td><a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a></td>
                            <td><?= e($row['phone']) ?></td>
                            <td><?= e($row['territory']) ?></td>
                            <td class="message-cell"><?= nl2br(e($row['message'])) ?></td>
                            <td><?= e($row['created_at']) ?></td>
                            <td>
                                <button
                                    class="btn danger small"
                                    type="submit"
                                    name="delete_id"
                                    value="<?= (int)$row['id'] ?>"
                                    onclick="return confirm('Delete this submission?');"
                                >Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bulk-actions">
                <button class="btn danger" type="submit" name="bulk_delete" value="1">Delete Selected</button>
            </div>
        </form>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="<?= $i === $page ? 'active' : '' ?>" href="?page=<?= $i ?><?= $search !== '' ? '&q=' . urlencode($search) : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>

<script>
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('input[name="selected_ids[]"]').forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
        });
    }
</script>
</body>
</html>
