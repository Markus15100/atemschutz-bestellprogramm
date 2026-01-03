<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/../config/db.php";

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: kategorien.php");
    exit;
}

// Prüfen ob Artikel existieren
$stmt = $pdo->prepare("SELECT COUNT(*) FROM artikel WHERE kategorie_id = ?");
$stmt->execute([$id]);
$anzahl = $stmt->fetchColumn();

if ($anzahl > 0) {
    // nicht löschen – zurück
    header("Location: kategorien.php?error=verwendet");
    exit;
}

// Löschen
$stmt = $pdo->prepare("DELETE FROM kategorien WHERE id = ?");
$stmt->execute([$id]);

header("Location: kategorien.php");
exit;
