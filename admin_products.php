<?php
session_start();
require_once 'includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Доступ запрещен.");
}
if (isset($_GET['approve'])) {
    $product_id = intval($_GET['approve']);
    $mysqli->query("UPDATE products SET status = 'active' WHERE id = $product_id");
    header("Location: admin_products.php");
}
if (isset($_GET['reject'])) {
    $product_id = intval($_GET['reject']);
    $mysqli->query("DELETE FROM products WHERE id = $product_id");
    header("Location: admin_products.php");
}

$result = $mysqli->query("SELECT * FROM products WHERE status = 'pending'");
$products = $result->fetch_all(MYSQLI_ASSOC);
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
        <h1>Объявления на модерации</h1>
        <?php if (empty($products)): ?>
            <p>Нет объявлений на модерацию.</p>
        <?php else: ?>
            <div class="products">
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Фото товара" width="150">
                        <?php endif; ?>
                        <h2><?= htmlspecialchars($product['name']) ?></h2>
                        <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> руб.</p>
                        <p><strong>Срок аренды:</strong> <?= htmlspecialchars($product['rental_period']) ?></p>
                        <p><strong>Категория:</strong> 
                            <?php
                            $category_id = $product['category_id'];
                            $category = $mysqli->query("SELECT name FROM categories WHERE id = $category_id")->fetch_assoc();
                            echo htmlspecialchars($category['name']);
                            ?>
                        </p>
                        <p><strong>Создатель:</strong> 
                            <?php
                            $user_id = $product['user_id'];
                            $user = $mysqli->query("SELECT username FROM users WHERE id = $user_id")->fetch_assoc();
                            echo htmlspecialchars($user['username']);
                            ?>
                        </p>

                        <a href="?approve=<?= $product['id'] ?>" class="approve-btn">Одобрить</a>
                        <a href="?reject=<?= $product['id'] ?>" class="reject-btn">Отклонить</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
