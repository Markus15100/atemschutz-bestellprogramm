<?php
require "../config/auth.php";
require "../config/db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: kategorien.php");
    exit;
}

/* Kategorie deaktivieren */
$stmt = $pdo->prepare("
    UPDATE kategorien
    SET aktiv = 0
    WHERE id = ?
");
$stmt->execute([$id]);

/* Optional: alle Artikel dieser Kategorie ebenfalls deaktivieren */
$stmt = $pdo->prepare("
    UPDATE artikel
    SET aktiv = 0
    WHERE kategorie_id = ?
");
$stmt->execute([$id]);

header("Location: kategorien.php?msg=deaktiviert");
exit;