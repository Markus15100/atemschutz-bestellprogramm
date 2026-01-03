<?php
session_start();
require __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["admin"] = $user["username"];
        header("Location: /ATEMSCHUTZ/admin/dashboard.php");
        exit;
    } else {
        $error = "Login fehlgeschlagen";
    }
}
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Login – Atemschutz FF Bodelshausen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container vh-100 d-flex justify-content-center align-items-center">
  <div class="card shadow" style="width: 380px;">
    <div class="card-body">

      <h4 class="text-center mb-4">Atemschutz<br>FF Bodelshausen</h4>

      <form method="post" action="login.php">
        <div class="mb-3">
          <label class="form-label">Benutzername</label>
          <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Passwort</label>
          <input type="password" class="form-control" name="password" required>
        </div>

        <?php if ($error): ?>
          <div class="text-danger mb-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <button class="btn btn-primary w-100">Anmelden</button>
      </form>

    </div>
  </div>
</div>
</body>
</html>
