<?php
require "../config/auth.php";
require "../config/db.php";

// Artikel inkl. Kategorie laden
$stmt = $pdo->query("
    SELECT a.*, k.name AS kategorie
    FROM artikel a
    JOIN kategorien k ON a.kategorie_id = k.id
    WHERE a.aktiv = 1
    ORDER BY a.name
");
$artikel = $stmt->fetchAll();
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
                    <td><?= (int)$a['ve_groesse'] ?> Stk / VE</td>
                    <td class="text-center">
                        <?php if(!empty($a['bild'])): ?>
                            <img src="../assets/uploads/artikel/<?= htmlspecialchars($a['bild']) ?>"
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
