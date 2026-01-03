<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/../config/db.php";

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: kategorien.php");
    exit;
}

// Kategorie laden
$stmt = $pdo->prepare("SELECT * FROM kategorien WHERE id = ?");
$stmt->execute([$id]);
$kategorie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kategorie) {
    header("Location: kategorien.php");
    exit;
}

$fehler = "";

// Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");

    if ($name === "") {
        $fehler = "Name darf nicht leer sein";
    } else {
        $stmt = $pdo->prepare("UPDATE kategorien SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);

        header("Location: kategorien.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Kategorie bearbeiten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4" style="max-width:600px;">
    <h4>Kategorie bearbeiten</h4>

    <?php if ($fehler): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($fehler) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($kategorie["name"]) ?>" required>
        </div>

        <div class="d-flex justify-content-between">
            <a href="kategorien.php" class="btn btn-secondary">Zurück</a>
            <button class="btn btn-primary">Speichern</button>
        </div>
    </form>
</div>

</body>
</html>
