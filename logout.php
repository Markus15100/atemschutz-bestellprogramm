<?php
/**
 * Logout – Session sauber beenden
 */

session_start();

// Alle Session-Variablen löschen
$_SESSION = [];

// Session zerstören
session_destroy();

// Optional: Session-Cookie löschen (sauber)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Zur Login-Seite zurück
header("Location: login.php");
exit;
