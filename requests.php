<?php
session_start();
require_once 'includes/db.php';

$result = $mysqli->query("SELECT rental_requests.*, users.username, categories.name AS category_name 
                          FROM rental_requests 
                          JOIN users ON rental_requests.user_id = users.id
                          JOIN categories ON rental_requests.category_id = categories.id
                          WHERE rental_requests.status = 'approved'
                          ORDER BY rental_requests.created_at DESC");
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявки на аренду</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Заявки на аренду</h1>
    <ul>
        <?php foreach ($requests as $request): ?>
            <li>
                <h2><?= htmlspecialchars($request['title']) ?> (<?= htmlspecialchars($request['category_name']) ?>)</h2>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($request['description'])) ?></p>
                <p><strong>Цена:</strong> <?= $request['price_min'] ?> - <?= $request['price_max'] ?> руб.</p>
                <p><strong>Срок аренды:</strong> <?= htmlspecialchars($request['duration']) ?></p>
                <p><strong>Автор:</strong> <?= htmlspecialchars($request['username']) ?></p>
                <a href="chat.php?receiver_id=<?= $request['user_id'] ?>">Связаться</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
