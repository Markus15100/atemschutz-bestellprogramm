<?php
require "../config/auth.php";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard – Atemschutz FF Bodelshausen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard FF Bodelshausen</h2>
        <span class="text-muted">
            Eingeloggt als <strong><?= htmlspecialchars($_SESSION['admin'] ?? 'Unbekannt') ?></strong>
        </span>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4">

        <!-- Artikel -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam display-4 text-primary"></i>
                    <h5 class="card-title mt-3">Artikel</h5>
                    <p class="card-text">Dräger Atemschutz</p>
                    <a href="artikel.php" class="btn btn-primary w-100">Verwalten</a>
                </div>
            </div>
        </div>

        <!-- Kategorien -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-folder2-open display-4 text-secondary"></i>
                    <h5 class="card-title mt-3">Kategorien</h5>
                    <p class="card-text">Masken, Geräte, Zubehör</p>
                    <a href="kategorien.php" class="btn btn-secondary w-100">Verwalten</a>
                </div>
            </div>
        </div>

        <!-- Neue Bestellung -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart-plus display-4 text-success"></i>
                    <h5 class="card-title mt-3">Neue Bestellung</h5>
                    <p class="card-text">Feuerwehr auswählen</p>
                    <a href="bestellung_add.php" class="btn btn-success w-100">Starten</a>
                </div>
            </div>
        </div>

        <!-- Bestellungen -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check display-4 text-info"></i>
                    <h5 class="card-title mt-3">Bestellungen</h5>
                    <p class="card-text">Übersicht & Details</p>
                    <a href="bestellungen.php" class="btn btn-info w-100">Ansehen</a>
                </div>
            </div>
        </div>

    </div>

    <hr class="my-4">

    <!-- Logout -->
    <div class="text-end">
        <a href="logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

</div>

</body>
</html>
