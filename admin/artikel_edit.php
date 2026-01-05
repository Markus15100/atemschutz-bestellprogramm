<?php
require "../config/auth.php";
require "../config/db.php";

if (!isset($_GET['id'])) {
    header("Location: artikel.php");
    exit;
}

$artikel_id = (int)$_GET['id'];
$fehler = '';
$success = '';

// Artikel laden
$stmt = $pdo->prepare("SELECT * FROM artikel WHERE id=?");
$stmt->execute([$artikel_id]);
$artikel = $stmt->fetch();

if (!$artikel) {
    header("Location: artikel.php");
    exit;
}

// Kategorien laden
$kategorien = $pdo->query("SELECT * FROM kategorien ORDER BY name")->fetchAll();

// Formular absenden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategorie_id        = (int)$_POST['kategorie_id'];
    $artikelnummer       = trim($_POST['artikelnummer']);
    $name                = trim($_POST['name']);
    $preis               = (float)$_POST['preis'];
    $verpackungseinheit  = $_POST['verpackungseinheit'];
    $ve_groesse          = (int)($_POST['ve_groesse'] ?? 1);

    if (!$kategorie_id || !$artikelnummer || !$name || $preis < 0) {
        $fehler = "Bitte alle Pflichtfelder korrekt ausfüllen.";
    } else {

        // Bild optional ersetzen
        $bildname = $artikel['bild'];
        $uploadDir = "../assets/uploads/artikel/";

        if (!empty($_FILES['bild']['name'])) {
            $ext = strtolower(pathinfo($_FILES['bild']['name'], PATHINFO_EXTENSION));
            $erlaubt = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $erlaubt)) {

                // altes Bild löschen
                if (!empty($artikel['bild']) && file_exists($uploadDir . $artikel['bild'])) {
                    unlink($uploadDir . $artikel['bild']);
                }

                // neues Bild speichern
                $bildname = uniqid('artikel_') . '.' . $ext;
                move_uploaded_file(
                    $_FILES['bild']['tmp_name'],
                    $uploadDir . $bildname
                );
            } else {
                $fehler = "Nur JPG, PNG oder WEBP erlaubt.";
            }
        }

        if (!$fehler) {
            $stmt = $pdo->prepare("
                UPDATE artikel
                SET kategorie_id=?, artikelnummer=?, name=?, preis=?, verpackungseinheit=?, ve_groesse=?, bild=?
                WHERE id=?
            ");
            $stmt->execute([
                $kategorie_id,
                $artikelnummer,
                $name,
                $preis,
                $verpackungseinheit,
                $ve_groesse,
                $bildname,
                $artikel_id
            ]);

            $success = "Artikel erfolgreich aktualisiert.";

            $stmt = $pdo->prepare("SELECT * FROM artikel WHERE id=?");
            $stmt->execute([$artikel_id]);
            $artikel = $stmt->fetch();
        }
    }
}

// Artikel deaktivieren
if (isset($_GET['deaktivieren']) && $_GET['deaktivieren'] == 1) {

    $stmt = $pdo->prepare("
        UPDATE artikel
        SET aktiv = 0
        WHERE id = ?
    ");
    $stmt->execute([$artikel_id]);

    header("Location: artikel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Artikel bearbeiten – AtemschutzBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Artikel bearbeiten</h2>
            <div>
                <a href="artikel.php" class="btn btn-secondary me-2">← Zurück</a>
                <a href="artikel_edit.php?id=<?= $artikel_id ?>&deaktivieren=1"
                    onclick="return confirm('Artikel wirklich deaktivieren?');"
                    class="btn btn-warning">
                    <i class="bi bi-archive"></i> Artikel deaktivieren
                </a>
            </div>
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
                        <option value="<?= $k['id'] ?>" <?= $k['id'] == $artikel['kategorie_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Artikelnummer *</label>
                <input type="text" name="artikelnummer" class="form-control" value="<?= htmlspecialchars($artikel['artikelnummer']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Artikelname *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($artikel['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Preis (€) *</label>
                <input type="number" step="0.01" name="preis" class="form-control" value="<?= htmlspecialchars($artikel['preis']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Verpackungseinheit *</label>
                <select name="verpackungseinheit" class="form-select" required>
                    <option value="Stück" <?= $artikel['verpackungseinheit'] == 'Stück' ? 'selected' : '' ?>>Stück</option>
                    <option value="VE" <?= $artikel['verpackungseinheit'] == 'VE' ? 'selected' : '' ?>>VE</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">VE-Größe *</label>
                <input type="number" name="ve_groesse" class="form-control" value="<?= (int)($artikel['ve_groesse'] ?? 1) ?>" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Artikelbild (optional)</label><br>
                <?php if (!empty($artikel['bild'])): ?>
                    <img src="../assets/uploads/artikel/<?= htmlspecialchars($artikel['bild']) ?>" width="100" class="img-thumbnail mb-2">
                <?php endif; ?>
                <input type="file" name="bild" class="form-control">
            </div>

            <button class="btn btn-primary">Änderungen speichern</button>
        </form>

    </div>

</body>

</html>