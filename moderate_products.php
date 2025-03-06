<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$result = $mysqli->query("
    SELECT p.id, p.name, p.description, p.price, p.rental_period, c.name AS category, u.username 
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'pending'
");

$pending_products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Модерация объявлений</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Модерация объявлений</h1>

        <?php if (empty($pending_products)): ?>
            <p>Нет объявлений на модерацию.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Срок аренды</th>
                        <th>Категория</th>
                        <th>Пользователь</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td><?= htmlspecialchars($product['price']) ?> руб.</td>
                            <td><?= htmlspecialchars($product['rental_period']) ?></td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td><?= htmlspecialchars($product['username']) ?></td>
                            <td>
                                <a href="approve_product.php?id=<?= $product['id'] ?>">Одобрить</a> |
                                <a href="reject_product.php?id=<?= $product['id'] ?>">Отклонить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
