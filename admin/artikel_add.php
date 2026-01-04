<?php
require "../config/auth.php";
require "../config/db.php";

$fehler = '';
$success = '';

// Kategorien laden
$kategorien = $pdo->query("SELECT * FROM kategorien ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kategorie_id        = (int)$_POST['kategorie_id'];
    $artikelnummer       = trim($_POST['artikelnummer']);
    $name                = trim($_POST['name']);
    $preis               = (float)$_POST['preis'];
    $verpackungseinheit  = $_POST['verpackungseinheit'];
    $ve_groesse          = (int)($_POST['ve_groesse'] ?? 1);

    // Grundvalidierung
    if (!$kategorie_id || !$artikelnummer || !$name || $preis <= 0) {
        $fehler = "Bitte alle Pflichtfelder korrekt ausfüllen.";
    } else {

        // Bild-Upload (optional)
        $bildname = null;
        if (!empty($_FILES['bild']['name'])) {
            $ext = strtolower(pathinfo($_FILES['bild']['name'], PATHINFO_EXTENSION));
            $erlaubt = ['jpg','jpeg','png','webp'];

            if (in_array($ext, $erlaubt)) {
                $bildname = uniqid('artikel_') . '.' . $ext;
                move_uploaded_file(
                    $_FILES['bild']['tmp_name'],
                    "../assets/images/" . $bildname
                );
            } else {
                $fehler = "Nur JPG, PNG oder WEBP erlaubt.";
            }
        }

        // In DB speichern
        if (!$fehler) {
            $stmt = $pdo->prepare("
                INSERT INTO artikel
                (kategorie_id, artikelnummer, name, preis, verpackungseinheit, ve_groesse, bild)
                VALUES (?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $kategorie_id,
                $artikelnummer,
                $name,
                $preis,
                $verpackungseinheit,
                $ve_groesse,
                $bildname
            ]);

            $success = "Artikel erfolgreich angelegt.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Artikel hinzufügen – AtemschutzBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Artikel hinzufügen</h2>
        <a href="artikel.php" class="btn btn-secondary">
            ← Zurück
        </a>
    </div>

    <?php if ($fehler): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($fehler) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Kategorie *</label>
            <select name="kategorie_id" class="form-select" required>
                <option value="">Bitte wählen</option>
                <?php foreach ($kategorien as $k): ?>
                    <option value="<?= $k['id'] ?>">
                        <?= htmlspecialchars($k['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Artikelnummer *</label>
            <input type="text" name="artikelnummer" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Artikelname *</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Preis (€) *</label>
            <input type="number" step="0.01" min="0" name="preis" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Verpackungseinheit *</label>
            <select name="verpackungseinheit" class="form-select" required>
                <option value="Stück">Stück</option>
                <option value="VE">VE</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">
                VE-Größe (z. B. 6 = 1 VE enthält 6 Stück) *
            </label>
            <input type="number" name="ve_groesse" class="form-control" value="1" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Artikelbild (optional)</label>
            <input type="file" name="bild" class="form-control">
        </div>

        <button class="btn btn-success">
            Artikel speichern
        </button>
    </form>

</div>

</body>
</html>
