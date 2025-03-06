<?php
session_start();
require_once 'includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ошибка: Неверный идентификатор товара.");
}

$product_id = intval($_GET['id']);

$stmt = $mysqli->prepare("
    SELECT p.*, c.name AS category_name, u.username AS owner_name, u.id AS owner_id
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ошибка: Товар не найден.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p><strong>Категория:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
        <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> ₽</p>
        <p><strong>Срок аренды:</strong> <?= htmlspecialchars($product['rental_period']) ?></p>
        <p><strong>Добавил:</strong> <?= htmlspecialchars($product['owner_name']) ?></p>

        <?php if (!empty($product['image'])): ?>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Фото товара" width="300">
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $product['owner_id']): ?>
            <a href="chat.php?receiver_id=<?= $product['owner_id'] ?>&product_id=<?= $product['id'] ?>">Связаться с владельцем</a>
        <?php endif; ?>
        <p><a href="products.php">Назад</a></p>
    </div>
</body>
</html>
