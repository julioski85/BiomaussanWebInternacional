<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if (is_post()) {
    $identifier = trim((string)($_POST['identifier'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $isValidUser = in_array($identifier, [ADMIN_LOGIN, ADMIN_EMAIL], true);

    if ($isValidUser && password_verify($password, ADMIN_PASSWORD_HASH)) {
        login_admin();
        redirect('dashboard.php');
    }

    $error = 'Invalid credentials. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Biomaussan International</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-bg">
<div class="admin-auth-wrap">
    <form method="post" class="card auth-card">
        <h1>Admin Login</h1>
        <p>Sign in to manage distributor form submissions.</p>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <label for="identifier">Username or email</label>
        <input id="identifier" type="text" name="identifier" required autocomplete="username">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">

        <button type="submit" class="btn primary">Sign in</button>
    </form>
</div>
</body>
</html>
