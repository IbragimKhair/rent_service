<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die('Объявление не найдено или у вас нет прав на его редактирование.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $rental_period = trim($_POST['rental_period']);
    $category_id = intval($_POST['category_id']);
    $image_path = $product['image'];

    if (empty($name) || empty($description) || empty($price) || empty($rental_period) || empty($category_id)) {
        $error = 'Заполните все поля!';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $image_name = 'product_' . time() . '_' . basename($_FILES['image']['name']);
            $image_path = 'uploads/' . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }
        $stmt = $mysqli->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, rental_period = ?, category_id = ?, image = ?, status = 'pending'
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ssdsissi", $name, $description, $price, $rental_period, $category_id, $image_path, $product_id, $user_id);

        if ($stmt->execute()) {
            $success = 'Объявление обновлено и отправлено на модерацию.';
        } else {
            $error = 'Ошибка при обновлении объявления.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать объявление</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Редактирование объявления</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="edit_product.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
            <label for="name">Название:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="description">Описание:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label for="price">Цена:</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required>

            <label for="rental_period">Срок аренды:</label>
            <input type="text" id="rental_period" name="rental_period" value="<?= htmlspecialchars($product['rental_period']) ?>" required>

            <label for="category_id">Категория:</label>
            <select id="category_id" name="category_id" required>
                <?php
                $categories = $mysqli->query("SELECT id, name FROM categories");
                while ($row = $categories->fetch_assoc()) {
                    $selected = ($row['id'] == $product['category_id']) ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                }
                ?>
            </select>

            <label for="image">Фото товара:</label>
            <?php if ($product['image']): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" width="100"><br>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Сохранить</button>
            <p><a href="index.php">Главная страница</a></p>
        </form>
    </div>
</body>
</html>
