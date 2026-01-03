<?php
require "../config/auth.php";
require "../config/db.php";

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $feuerwehr = trim($_POST['feuerwehr']);

    if (!$feuerwehr) {
        $fehler = "Feuerwehr bitte angeben.";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO bestellungen (feuerwehr, gesamt)
            VALUES (?, 0)
        ");
        $stmt->execute([$feuerwehr]);

        $id = $pdo->lastInsertId();

        header("Location: bestellung_detail.php?id=" . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neue Bestellung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h2>Neue Bestellung anlegen</h2>
    <a href="bestellungen.php" class="btn btn-secondary mb-3">← Zurück</a>

    <?php if ($fehler): ?>
        <div class="alert alert-danger"><?= $fehler ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Feuerwehr *</label>
            <input type="text" name="feuerwehr" class="form-control" required>
        </div>

        <button class="btn btn-success">
            Bestellung anlegen
        </button>
    </form>

</div>

</body>
</html>
