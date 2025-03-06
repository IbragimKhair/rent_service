<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$result = $mysqli->query("SELECT * FROM products WHERE user_id = $user_id");

$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои объявления</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Ваши объявления</h1>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Цена: <?= htmlspecialchars($product['price']) ?> руб.</p>
                    <p>Срок аренды: <?= htmlspecialchars($product['rental_period']) ?></p>
                    <p>Статус: <?= htmlspecialchars($product['status']) ?></p>
                    <a href="edit_product.php?id=<?= $product['id'] ?>">Редактировать</a>
                    <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Удалить объявление?')">Удалить</a>
                </div>
            <?php endforeach; ?>
            <p><a href="index.php">Главная страница</a></p>
        </div>
    </div>
</body>
</html>
