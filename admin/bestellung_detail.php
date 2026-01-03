<?php
require "../config/auth.php";
require "../config/db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Ungültige Bestellung");

/* Bestellung laden */
$stmt = $pdo->prepare("SELECT * FROM bestellungen WHERE id = ?");
$stmt->execute([$id]);
$bestellung = $stmt->fetch();
if (!$bestellung) die("Bestellung nicht gefunden");

/* Artikelliste für Dropdown */
$artikelListe = $pdo->query("
    SELECT id, artikelnummer, name, preis, ve_groesse
    FROM artikel
    ORDER BY name
")->fetchAll();

/* Artikel hinzufügen oder Bestellung abschließen */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Bestellung abschließen
    if (isset($_POST['aktion']) && $_POST['aktion'] === 'abschliessen') {
        $stmt = $pdo->prepare("UPDATE bestellungen SET status='abgeschlossen' WHERE id=?");
        $stmt->execute([$id]);
        header("Location: bestellung_detail.php?id=" . $id);
        exit;
    }

    // Artikel hinzufügen (nur wenn offen)
    if ($bestellung['status'] === 'offen') {
        $artikel_id = (int)($_POST['artikel_id'] ?? 0);
        $menge = (int)($_POST['menge'] ?? 0);
        $einheit = $_POST['einheit'] ?? 'Stück';

        if ($artikel_id && $menge > 0) {
            $stmt = $pdo->prepare("SELECT preis FROM artikel WHERE id=?");
            $stmt->execute([$artikel_id]);
            $artikel = $stmt->fetch();
            if ($artikel) {
                $stmt = $pdo->prepare("
                    INSERT INTO bestellpositionen
                    (bestellung_id, artikel_id, menge, einheit, einzelpreis)
                    VALUES (?,?,?,?,?)
                ");
                $stmt->execute([$id, $artikel_id, $menge, $einheit, $artikel['preis']]);
            }
        }
        header("Location: bestellung_detail.php?id=" . $id);
        exit;
    }
}

/* Positionen laden */
$stmt = $pdo->prepare("
    SELECT p.id, p.menge, p.einheit, p.einzelpreis,
           a.name, a.artikelnummer, a.ve_groesse
    FROM bestellpositionen p
    JOIN artikel a ON a.id = p.artikel_id
    WHERE p.bestellung_id = ?
");
$stmt->execute([$id]);
$positionen = $stmt->fetchAll();

/* Gesamt neu berechnen */
$gesamt = 0;
foreach ($positionen as $pos) {
    $gesamt += $pos['menge'] * $pos['einzelpreis'];
}
$stmt = $pdo->prepare("UPDATE bestellungen SET gesamt=? WHERE id=?");
$stmt->execute([$gesamt, $id]);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Bestellung #<?= $bestellung['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Bestellung #<?= $bestellung['id'] ?></h2>
            <div>
                <a href="bestellungen.php" class="btn btn-secondary">← Bestellungen</a>
                <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
            </div>
        </div>

        <!-- Bestellkopf -->
        <div class="card mb-4">
            <div class="card-body">
                <strong>Feuerwehr:</strong> <?= htmlspecialchars($bestellung['feuerwehr']) ?><br>
                <strong>Datum:</strong> <?= date('d.m.Y H:i', strtotime($bestellung['datum'])) ?><br>
                <strong>Gesamt:</strong> <?= number_format($gesamt, 2, ',', '.') ?> €<br>
                <strong>Status:</strong> <?= htmlspecialchars($bestellung['status']) ?>
            </div>
        </div>

        <!-- Artikel hinzufügen -->
        <?php if ($bestellung['status'] === 'offen'): ?>
            <div class="card mb-4">
                <div class="card-header">Artikel hinzufügen</div>
                <div class="card-body">
                    <form method="post" class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Artikel</label>
                            <select name="artikel_id" class="form-select" required>
                                <option value="">Bitte wählen</option>
                                <?php foreach ($artikelListe as $a): ?>
                                    <option value="<?= $a['id'] ?>">
                                        <?= htmlspecialchars($a['artikelnummer'] . " – " . $a['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Menge</label>
                            <input type="number" name="menge" min="1" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Einheit</label>
                            <select name="einheit" class="form-select">
                                <option value="Stück">Stück</option>
                                <option value="VE">VE</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-success">Artikel hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mb-4">
                Keine Änderungen möglich – Bestellung abgeschlossen ✅
            </div>
        <?php endif; ?>

        <!-- Positionen -->
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Art.-Nr.</th>
                    <th>Artikel</th>
                    <th>Menge</th>
                    <th>Einheit</th>
                    <th>VE-Größe</th>
                    <th>Preis</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($positionen)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Keine Artikel in dieser Bestellung</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($positionen as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['artikelnummer']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= $p['menge'] ?></td>
                            <td><?= $p['einheit'] ?></td>
                            <td><?= (int)$p['ve_groesse'] ?> Stk / VE</td>
                            <td><?= number_format($p['menge'] * $p['einzelpreis'], 2, ',', '.') ?> €</td>
                            <td>
                                <?php if ($bestellung['status'] === 'offen'): ?>
                                    <a href="bestellposition_delete.php?id=<?= $p['id'] ?>&bid=<?= $id ?>"
                                        onclick="return confirm('Position löschen?')"
                                        class="btn btn-sm btn-danger">✕</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Bestellung abschließen -->
        <?php if ($bestellung['status'] === 'offen'): ?>
            <div class="text-end my-3">
                <form method="post">
                    <input type="hidden" name="aktion" value="abschliessen">
                    <button class="btn btn-success btn-lg">Bestellung abschließen</button>
                </form>
            </div>
        <?php endif; ?>

        <a href="bestellung_pdf.php?id=<?= $bestellung['id'] ?>"
            target="_blank"
            class="btn btn-danger">
            🖨 Bestellung als PDF
        </a>

    </div>

</body>

</html>