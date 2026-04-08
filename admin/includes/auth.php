<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

if (!is_logged_in()) {
    redirect('login.php');
}
