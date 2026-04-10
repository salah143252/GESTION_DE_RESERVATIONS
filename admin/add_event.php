<?php
require '../header.php';
require '../config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $date     = $_POST['date_event'];
    $places   = (int) $_POST['nbPlaces'];
    $price    = (float) $_POST['price'];
    $location = trim($_POST['location']);

    if (!$title || !$date || !$location || $places < 1) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title, date_event, nbPlaces, price, location) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $date, $places, $price, $location]);

        $_SESSION['msg'] = "Événement ajouté avec succès.";
        $_SESSION['msg_type'] = 'success';
        header('Location: index.php');
        exit;
    }
}
?>
<div class="container">
    <div class="form-box" style="max-width:500px">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px">
            <h2>Ajouter un événement</h2>
            <a href="index.php" class="btn btn-gray btn-sm">← Retour</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Titre</label>
                <input type="text" name="title" required placeholder="Nom de l'événement">
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date_event" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Lieu</label>
                <input type="text" name="location" required placeholder="Ville, salle...">
            </div>
            <div class="form-group">
                <label>Nombre de places</label>
                <input type="number" name="nbPlaces" required min="1" placeholder="ex: 100">
            </div>
            <div class="form-group">
                <label>Prix (€)</label>
                <input type="number" name="price" step="0.01" min="0" placeholder="ex: 25.00">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Ajouter l'événement</button>
        </form>
    </div>
</div>
</body>
</html>