<?php
require "../config/auth.php";
require "../config/db.php";

$stmt = $pdo->query("
    SELECT 
        b.id,
        b.datum,
        b.feuerwehr,
        b.gesamt,
        COUNT(p.id) AS positionen
    FROM bestellungen b
    LEFT JOIN bestellpositionen p ON p.bestellung_id = b.id
    GROUP BY b.id
    ORDER BY b.datum DESC
");

$bestellungen = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Bestellungen – AtemschutzBO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Bestellungen</h2>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>



        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Datum</th>
                    <th>Feuerwehr</th>
                    <th>Positionen</th>
                    <th>Gesamt</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bestellungen)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Keine Bestellungen vorhanden
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bestellungen as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($b['datum'])) ?></td>
                            <td><?= htmlspecialchars($b['feuerwehr']) ?></td>
                            <td><?= (int)$b['positionen'] ?></td>
                            <td><?= number_format($b['gesamt'], 2, ',', '.') ?> €</td>
                            <td class="text-end">
                                <a href="bestellung_detail.php?id=<?= $b['id'] ?>"
                                    class="btn btn-sm btn-primary">
                                    Details
                                </a>
                                <a href="bestellung_delete.php?id=<?= $b['id'] ?>"
                                    onclick="return confirm('Bestellung wirklich löschen? Alle Positionen werden entfernt!');"
                                    class="btn btn-sm btn-danger">
                                    🗑 Löschen
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>

</html>