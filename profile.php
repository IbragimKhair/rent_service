<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT username, email, phone, bio, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $bio, $profile_picture);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function toggleEdit() {
            document.getElementById('profile-info').style.display = 'none';
            document.getElementById('edit-form').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Мой профиль</h1>

        <div id="profile-info">
            <div>
                <h3>Фото профиля:</h3>
                <img src="uploads/<?= htmlspecialchars($profile_picture) ?>" alt="Фото профиля" width="150">
            </div>
            <div>
                <h3>Имя: <?= htmlspecialchars($username) ?></h3>
            </div>
            <div>
                <h3>Email: <?= htmlspecialchars($email) ?></h3>
            </div>
            <div>
                <h3>Телефон: <?= $phone ? htmlspecialchars($phone) : 'Не указан' ?></h3>
            </div>
            <div>
                <h3>О себе: <?= $bio ? nl2br(htmlspecialchars($bio)) : 'Не указано' ?></h3>
            </div>
            <button onclick="toggleEdit()">Редактировать</button>
        </div>

        <div id="edit-form" style="display: none;">
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div>
                    <h3>Фото профиля:</h3>
                    <input type="file" name="profile_picture" accept="image/png">
                </div>
                <div>
                    <h3>Имя:</h3>
                    <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
                </div>
                <div>
                    <h3>Email:</h3>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
                </div>
                <div>
                    <h3>Телефон:</h3>
                    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">
                </div>
                <div>
                    <h3>О себе:</h3>
                    <textarea name="bio" rows="4"><?= htmlspecialchars($bio) ?></textarea>
                </div>
                <button type="submit">Сохранить изменения</button>
            </form>
        </div>

        <a href="my_products.php">Мои объявления</a>
        <p><a href="index.php">Главная страница</a></p>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
