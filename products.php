<?php
session_start();
require_once 'includes/db.php';

$result = $mysqli->query("
    SELECT id, name, price, image FROM products WHERE status = 'active' ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список объявлений</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <p><a href="index.php">Главная страница</a></p>
        <h1>Список объявлений</h1>
        <div class="products">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product">
                    <a href="product_detail.php?id=<?= $product['id'] ?>">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="200">
                        <?php endif; ?>
                        <h2><?= htmlspecialchars($product['name']) ?></h2>
                        <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> ₽</p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
