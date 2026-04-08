<?php

declare(strict_types=1);

// Database configuration.
const DB_HOST = 'localhost';
const DB_NAME = 'u979944047_bioint';
const DB_USER = 'u979944047_bioint';
const DB_PASS = 'Juliocesar1234$';

// Admin login configuration.
// Demo password for this hash: Admin123!Change
// Generate a new hash with: php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
const ADMIN_LOGIN = 'admin';
const ADMIN_EMAIL = 'admin@biomaussan.com';
const ADMIN_PASSWORD_HASH = '$2y$12$i3wTcfnt8QB9d7EhmArMKu34DS3liG4wVN2mz2Zyj6WJdv3LRz8la';

// General configuration.
const APP_TIMEZONE = 'UTC';
const ITEMS_PER_PAGE = 15;

date_default_timezone_set(APP_TIMEZONE);

function db(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($connection->connect_error) {
        http_response_code(500);
        exit('Database connection error.');
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}
