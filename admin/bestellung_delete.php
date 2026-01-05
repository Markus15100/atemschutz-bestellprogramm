<?php
require "../config/auth.php";
require "../config/db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: bestellungen.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Positionen löschen
    $stmt = $pdo->prepare("DELETE FROM bestellpositionen WHERE bestellung_id = ?");
    $stmt->execute([$id]);

    // 2. Bestellung löschen
    $stmt = $pdo->prepare("DELETE FROM bestellungen WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Fehler beim Löschen der Bestellung");
}

header("Location: bestellungen.php");
exit;
