<?php
require 'header.php';
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

        $_SESSION['msg'] = "Compte créé ! Vous pouvez vous connecter.";
        $_SESSION['msg_type'] = 'success';
        header('Location: login.php');
        exit;
    }
}
?>
<div class="container">
    <div class="form-box">
        <h2>Créer un compte</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="name" required placeholder="Votre nom">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="votre@email.com">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">S'inscrire</button>
        </form>
        <p style="margin-top:14px; font-size:0.85rem; color:var(--gray)">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>
</body>
</html>