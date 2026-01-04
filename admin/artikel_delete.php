<?php
require "../config/auth.php";
require "../config/db.php";

/*
 * artikel_delete.php
 * ------------------
 * KEIN echtes Löschen!
 * Artikel werden nur deaktiviert (Soft-Delete),
 * damit bestehende Bestellungen korrekt bleiben.
 */

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Ungültige Artikel-ID");
}

/* Artikel prüfen */
$stmt = $pdo->prepare("
    SELECT id, name, aktiv
    FROM artikel
    WHERE id = ?
");
$stmt->execute([$id]);
$artikel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artikel) {
    die("Artikel nicht gefunden");
}

/* Falls Artikel bereits inaktiv ist → einfach zurück */
if ((int)$artikel['aktiv'] === 0) {
    header("Location: artikel.php");
    exit;
}

/* Artikel deaktivieren */
$stmt = $pdo->prepare("
    UPDATE artikel
    SET aktiv = 0
    WHERE id = ?
");
$stmt->execute([$id]);

/* Zurück zur Artikelübersicht */
header("Location: artikel.php");
exit;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Artikelverwaltung – AtemschutzBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Artikelverwaltung</h2>
        <a href="dashboard.php" class="btn btn-secondary">
            ← Dashboard
        </a>
    </div>

    <a href="artikel_add.php" class="btn btn-success mb-3">
        + Artikel hinzufügen
    </a>

    <!-- Artikeltabelle -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Artikelnummer</th>
                    <th>Name</th>
                    <th>Kategorie</th>
                    <th>Preis</th>
                    <th>VE</th>
                    <th>VE-Größe</th>
                    <th>Bild</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($artikel)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        Keine Artikel vorhanden
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($artikel as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['artikelnummer']) ?></td>
                    <td><?= htmlspecialchars($a['name']) ?></td>
                    <td><?= htmlspecialchars($a['kategorie']) ?></td>
                    <td><?= number_format($a['preis'], 2, ',', '.') ?> €</td>
                    <td><?= htmlspecialchars($a['verpackungseinheit']) ?></td>
                    <td><?= (int)($a['ve_groesse'] ?? 1) ?> Stk / VE</td>
                    <td class="text-center">
                        <?php if(!empty($a['bild'])): ?>
                            <img src="../assets/images/<?= htmlspecialchars($a['bild']) ?>"
                                 width="60" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="artikel_edit.php?id=<?= $a['id'] ?>"
                           class="btn btn-sm btn-primary">
                            Bearbeiten
                        </a>
                        <a href="artikel_delete.php?id=<?= $a['id'] ?>"
                           onclick="return confirm('Artikel wirklich löschen?');"
                           class="btn btn-sm btn-danger">
                            Löschen
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
