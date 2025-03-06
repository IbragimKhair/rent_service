<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_hash = trim($_POST['password_hash']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password_hash) || empty($confirm_password)) {
        $error = 'Пожалуйста, заполните все обязательные поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Неверный формат email.';
    } elseif ($password_hash !== $confirm_password) {
        $error = 'Пароли не совпадают.';
    } elseif (strlen($password_hash) < 6 || !preg_match('/[a-z]/', $password_hash) || !preg_match('/[A-Z]/', $password_hash)) {
        $error = 'Пароль должен содержать минимум 6 символов, включая заглавные и строчные буквы.';
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die('Ошибка подготовки запроса: ' . $mysqli->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Этот email уже зарегистрирован.';
        } else {
            $hashed_password = password_hash($password_hash, PASSWORD_BCRYPT);

            $stmt = $mysqli->prepare("
                INSERT INTO users (username, email, password_hash, phone, created_at) 
                VALUES (?, ?, ?, NULL, NOW())
            ");
            if (!$stmt) {
                die('Ошибка подготовки запроса: ' . $mysqli->error);
            }

            $stmt->bind_param('sss', $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $stmt->close();
                header('Location: login.php');
                exit();
            } else {
                $error = 'Ошибка при регистрации: ' . $mysqli->error;
            }
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
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="username">Имя:</label>
            <input type="text" id="username" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>

            <label for="password_hash">Пароль:</label>
            <input type="password" id="password_hash" name="password_hash" required>

            <label for="confirm_password">Подтверждение пароля:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Есть аккаунт? <a href="login.php">Войти</a></p>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
