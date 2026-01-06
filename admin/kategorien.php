<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/../config/db.php";

// Kategorien laden
$stmt = $pdo->query("SELECT * FROM kategorien WHERE aktiv = 1 ORDER BY name");
$kategorien = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Kategorien – Atemschutz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Kategorien</h3>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>

        <!-- Neu anlegen -->
        <div class="mb-3">
            <a href="kategorie_add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Neue Kategorie
            </a>
        </div>

        <!-- Tabelle -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-end">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kategorien)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Keine Kategorien vorhanden
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kategorien as $kat): ?>
                                <tr>
                                    <td><?= $kat['id'] ?></td>
                                    <td><?= htmlspecialchars($kat['name']) ?></td>
                                    <td class="text-end">
                                        <a href="kategorie_edit.php?id=<?= $kat['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="kategorie_delete.php?id=<?= $kat['id'] ?>"
                                            onclick="return confirm('Kategorie wirklich deaktivieren?');"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-archive"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>