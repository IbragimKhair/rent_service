<?php
require_once 'includes/db.php';

$result = $mysqli->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_categories.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Категории товаров</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Категории товаров</h1>
        
        <ul>
            <?php foreach ($categories as $category): ?>
                <li><?= htmlspecialchars($category['name']) ?></li>
            <?php endforeach; ?>
        </ul>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
