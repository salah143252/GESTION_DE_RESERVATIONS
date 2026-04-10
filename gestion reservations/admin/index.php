<?php
require '../header.php';
require '../config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$search = trim($_GET['q'] ?? '');

$sql = "
    SELECT e.*, COUNT(r.id) AS nb_reservations
    FROM events e
    LEFT JOIN reservations r ON e.id = r.event_id
";

if ($search) {
    $sql .= " WHERE e.title LIKE :q";
}

$sql .= " GROUP BY e.id ORDER BY e.date_event DESC";

$stmt = $pdo->prepare($sql);
if ($search) $stmt->bindValue(':q', "%$search%");
$stmt->execute();
$events = $stmt->fetchAll();
?>
<div class="container">
    <div class="top-bar">
        <h1>🛠 Admin — Événements</h1>
        <a href="add_event.php" class="btn btn-success btn-sm">+ Ajouter un événement</a>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['msg'] ?></div>
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <form method="GET" class="search-bar">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un événement...">
        <button type="submit" class="btn btn-primary btn-sm">Rechercher</button>
        <?php if ($search): ?>
            <a href="index.php" class="btn btn-gray btn-sm">Réinitialiser</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Date</th>
                <th>Lieu</th>
                <th>Prix</th>
                <th>Places dispo.</th>
                <th>Réservations</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['title']) ?></td>
                <td><?= date('d/m/Y', strtotime($e['date_event'])) ?></td>
                <td><?= htmlspecialchars($e['location']) ?></td>
                <td><?= number_format($e['price'], 2) ?> €</td>
                <td><?= $e['nbPlaces'] ?></td>
                <td><?= $e['nb_reservations'] ?></td>
                <td>
                    <?php if ($e['nbPlaces'] == 0): ?>
                        <span class="badge-sold">Sold Out</span>
                    <?php else: ?>
                        <span style="color:var(--success); font-size:0.82rem; font-weight:600;">Disponible</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>