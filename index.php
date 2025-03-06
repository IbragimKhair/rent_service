<?php
session_start();
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_role = null;

if ($user_id) {
    $stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_role = $user['role'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сервис аренды вещей</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать в сервис аренды вещей</h1>
        <p><a href="info.php">О сервисе</a></p>
        <?php if ($user_id): ?>
            <p><a href="profile.php">Профиль</a></p>
            <a href="my_products.php">Мои объявления</a>
        <?php else: ?>
            <p><a href="register.php">Регистрация</a></p>
            <p><a href="login.php">Авторизация</a></p>
        <?php endif; ?>
        <p><a href="categories.php">Каталог</a></p>
        <p><a href="add_request.php">Сдать в аренду</a></p>
        <p><a href="products.php">Товары</a></p>
        <a href="messages_list.php">Мои сообщения</a>
        <?php if ($user_role === 'admin'): ?>
            <p><a href="admin_products.php">Заявки на аренду</a></p>
        <?php endif; ?>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
