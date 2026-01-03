<?php
require "../config/auth.php";
require "../config/db.php";

$id = (int)$_GET['id'];
$bid = (int)$_GET['bid'];

$stmt = $pdo->prepare("DELETE FROM bestellpositionen WHERE id=?");
$stmt->execute([$id]);

header("Location: bestellung_detail.php?id=" . $bid);
exit;
