<?php
require 'header.php';
require 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.id, r.reservation_date, e.title, e.date_event, e.location, e.price
    FROM reservations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>
<div class="container">
    <h1>Mes réservations</h1>

    <?php if (empty($reservations)): ?>
        <p style="color:var(--gray)">Vous n'avez pas encore de réservations. <a href="index.php">Voir les événements</a></p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Événement</th>
                <th>Date de l'événement</th>
                <th>Lieu</th>
                <th>Prix</th>
                <th>Réservé le</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['date_event'])) ?></td>
                <td><?= htmlspecialchars($r['location']) ?></td>
                <td><?= number_format($r['price'], 2) ?> €</td>
                <td><?= date('d/m/Y H:i', strtotime($r['reservation_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>