<?php
/**
 * Database connection file
 * Dilshan Stores - Automatic Bill Calculation System
 *
 * Update the credentials below to match your local XAMPP/WAMP MySQL setup.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'dilshan_stores';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage() .
        '<br>Make sure MySQL is running and you have imported database.sql.');
}
