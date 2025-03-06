<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);

    if (!empty($category_name)) {
        $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param('s', $category_name);
        $stmt->execute();
        $stmt->close();
    }
}
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $stmt->close();
    header('Location: admin_categories.php');
    exit();
}
$result = $mysqli->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление категориями</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Управление категориями</h1>
        
        <form action="admin_categories.php" method="POST">
            <input type="text" name="category_name" placeholder="Название категории" required>
            <button type="submit">Добавить</button>
        </form>

        <h2>Список категорий</h2>
        <ul>
            <?php foreach ($categories as $category): ?>
                <li>
                    <?= htmlspecialchars($category['name']) ?>
                    <a href="admin_categories.php?delete_id=<?= $category['id'] ?>" onclick="return confirm('Удалить категорию?')">Удалить</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
