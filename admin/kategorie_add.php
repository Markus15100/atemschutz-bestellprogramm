<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/../config/db.php";

$fehler = "";

// Formular verarbeitet
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");

    if ($name === "") {
        $fehler = "Bitte einen Kategorienamen eingeben";
    } else {
        $stmt = $pdo->prepare("INSERT INTO kategorien (name) VALUES (?)");
        $stmt->execute([$name]);

        // Zurück zur Übersicht
        header("Location: kategorien.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Kategorie anlegen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4" style="max-width: 600px;">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Neue Kategorie</h4>
        <a href="kategorien.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Zurück
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($fehler): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($fehler) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Kategoriename</label>
                    <input type="text" name="name" class="form-control" required autofocus>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="kategorien.php" class="btn btn-outline-secondary">
                        Abbrechen
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Speichern
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

</body>
</html>
