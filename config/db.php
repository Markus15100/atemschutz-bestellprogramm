<?php
/**
 * Datenbankverbindung
 * Projekt: AtemschutzBO
 */

$DB_HOST = 'localhost';
$DB_NAME = 'draeger';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Fehler als Exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch als Array
    PDO::ATTR_EMULATE_PREPARES   => false,                 // echte Prepared Statements
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Niemals DB-Daten im Klartext anzeigen!
    die('Datenbankverbindung fehlgeschlagen.');
}
