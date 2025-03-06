<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля.';
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Неверный формат email.';
        } else {
            $stmt = $mysqli->prepare("SELECT id, password_hash FROM users WHERE email = ?");
            if (!$stmt) {
                die('Ошибка запроса: ' . $mysqli->error);
            }

            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $password_hash);
                $stmt->fetch();
                if (password_verify($password, $password_hash)) {
                    $_SESSION['user_id'] = $user_id;
                    if ($email === 'admin@example.com') {
                        $_SESSION['role'] = 'admin';
                        header('Location: index.php');
                    } else {
                        $_SESSION['role'] = 'user';
                        header('Location: profile.php');
                    }
                    exit();
                } else {
                    $error = 'Неверный логин или пароль.';
                }
            } else {
                $error = 'Неверный логин или пароль.';
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Авторизация</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="email">E-mail:</label>
            <input type="text" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
            
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Войти</button>
        </form>
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
