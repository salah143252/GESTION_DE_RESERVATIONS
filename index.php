<?php
require 'header.php';
require 'config.php';

$stmt = $pdo->query("SELECT * FROM events WHERE date_event >= CURDATE() ORDER BY date_event ASC");
$events = $stmt->fetchAll();
?>
<div class="container">
    <h1>Événements à venir</h1>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['msg'] ?></div>
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <?php if (empty($events)): ?>
        <p style="color: var(--gray);">Aucun événement disponible pour le moment.</p>
    <?php else: ?>
    <div class="cards-grid">
        <?php foreach ($events as $e): ?>
        <div class="card">
            <?php if ($e['nbPlaces'] == 0): ?>
                <span class="badge-sold">Complet</span>
            <?php endif; ?>
            <h3><?= htmlspecialchars($e['title']) ?></h3>
            <p class="meta">📅 <?= date('d/m/Y', strtotime($e['date_event'])) ?></p>
            <p class="meta">📍 <?= htmlspecialchars($e['location']) ?></p>
            <p class="meta">🪑 <?= $e['nbPlaces'] ?> place(s) restante(s)</p>
            <p class="price"><?= number_format($e['price'], 2) ?> €</p>

            <?php if ($e['nbPlaces'] > 0): ?>
                <?php if (isLoggedIn()): ?>
                    <a href="reserver.php?id=<?= $e['id'] ?>" class="btn btn-primary btn-sm">Réserver</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-gray btn-sm">Connexion pour réserver</a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn-danger btn-sm" disabled>Complet</button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>