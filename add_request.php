<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$result = $mysqli->query("SELECT id, name FROM categories");
$categories = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $rental_period = trim($_POST['rental_period']);
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = 'uploads/' . time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imageName)) {
            $image = $imageName;
        } else {
            $error = "Ошибка загрузки фото!";
        }
    }

    if (empty($name) || empty($price) || empty($rental_period)) {
        $error = "Заполните все обязательные поля!";
    } else {
        $stmt = $mysqli->prepare("
            INSERT INTO products (user_id, category_id, name, description, price, rental_period, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iissdss", $user_id, $category_id, $name, $description, $price, $rental_period, $image);

        if ($stmt->execute()) {
            $success = "Заявка отправлена на модерацию!";
        } else {
            $error = "Ошибка при отправке заявки!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить заявку</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Оставить заявку на добавление товара</h1>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="add_request.php" method="POST" enctype="multipart/form-data">
            <label for="category_id">Категория:</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="name">Название товара:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Описание:</label>
            <textarea id="description" name="description"></textarea>

            <label for="price">Цена (руб):</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="rental_period">Срок аренды (дни):</label>
            <input type="text" id="rental_period" name="rental_period" required>

            <label for="image">Фото товара:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Отправить заявку</button>
            <p><a href="index.php">Главная страница</a></p>
        </form>
    </div>
</body>
</html>
