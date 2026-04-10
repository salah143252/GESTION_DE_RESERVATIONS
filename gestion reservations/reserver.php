<?php
require 'header.php';
require 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<div class='container'><p>Événement introuvable.</p></div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Re-fetch to get fresh nbPlaces
    $stmt = $pdo->prepare("SELECT nbPlaces FROM events WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $fresh = $stmt->fetch();

    if ($fresh['nbPlaces'] <= 0) {
        $_SESSION['msg'] = "Désolé, cet événement est complet.";
        $_SESSION['msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    // Check if already reserved
    $check = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND event_id = ?");
    $check->execute([$_SESSION['user_id'], $id]);
    if ($check->rowCount() > 0) {
        $_SESSION['msg'] = "Vous avez déjà réservé cet événement.";
        $_SESSION['msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)")
        ->execute([$_SESSION['user_id'], $id]);

    $pdo->prepare("UPDATE events SET nbPlaces = nbPlaces - 1 WHERE id = ?")
        ->execute([$id]);

    $_SESSION['msg'] = "Réservation confirmée pour « {$event['title']} » !";
    $_SESSION['msg_type'] = 'success';
    header('Location: index.php');
    exit;
}
?>
<div class="container">
    <div class="form-box">
        <h2>Confirmer la réservation</h2>
        <div style="background:var(--light); border-radius:8px; padding:16px; margin-bottom:20px;">
            <p><strong><?= htmlspecialchars($event['title']) ?></strong></p>
            <p class="meta" style="color:var(--gray); font-size:0.85rem; margin-top:6px">
                📅 <?= date('d/m/Y', strtotime($event['date_event'])) ?> &nbsp;|&nbsp;
                📍 <?= htmlspecialchars($event['location']) ?> &nbsp;|&nbsp;
                💰 <?= number_format($event['price'], 2) ?> €
            </p>
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-primary" style="width:100%">Confirmer</button>
        </form>
        <a href="index.php" style="display:block; margin-top:12px; font-size:0.85rem; color:var(--gray); text-align:center;">Annuler</a>
    </div>
</div>
</body>
</html>