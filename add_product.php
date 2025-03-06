<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $rental_period = trim($_POST['rental_period']);
    $category_id = intval($_POST['category_id']);
    $user_id = $_SESSION['user_id'];

    if (empty($name) || empty($description) || empty($price) || empty($rental_period) || empty($category_id)) {
        $error = 'Заполните все поля!';
    } else {
        $stmt = $mysqli->prepare("
            INSERT INTO products (name, description, price, rental_period, category_id, user_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("ssdsii", $name, $description, $price, $rental_period, $category_id, $user_id);

        if ($stmt->execute()) {
            $success = 'Ваше объявление отправлено на модерацию.';
        } else {
            $error = 'Ошибка при добавлении объявления.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить объявление</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Добавить объявление</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="add_product.php" method="POST">
            <label for="name">Название:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Описание:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Цена:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="rental_period">Срок аренды:</label>
            <input type="text" id="rental_period" name="rental_period" required>

            <label for="category_id">Категория:</label>
            <select id="category_id" name="category_id" required>
                <?php
                $result = $mysqli->query("SELECT id, name FROM categories");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <button type="submit">Отправить на модерацию</button>
        </form>
    </div>
</body>
</html>
