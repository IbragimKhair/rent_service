<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("
    SELECT DISTINCT users.id, users.username, messages.product_id
    FROM messages 
    JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id) 
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.id != ?
");
$stmt->bind_param('iii', $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$chats = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои сообщения</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Мои сообщения</h1>
        <ul>
            <?php if (empty($chats)): ?>
                <p>У вас пока нет сообщений.</p>
            <?php else: ?>
                <?php foreach ($chats as $chat): ?>
                    <li>
                        <a href="chat.php?receiver_id=<?= $chat['id'] ?>&product_id=<?= $chat['product_id'] ?>">
                            Чат с <?= htmlspecialchars($chat['username']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <p><a href="index.php">Главная страница</a></p>
    </div>
</body>
</html>
