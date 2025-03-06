<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$receiver_id || !$product_id) {
    die("Ошибка: Неверные данные чата.");
}

$stmt = $mysqli->prepare("
    SELECT p.name AS product_name, p.image AS product_image, u.username AS owner_name 
    FROM products p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Ошибка: Товар не найден.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Ошибка запроса: " . $mysqli->error);
    }
    $stmt->bind_param('iiis', $user_id, $receiver_id, $product_id, $message);
    $stmt->execute();
    $stmt->close();
    header("Location: chat.php?receiver_id=$receiver_id&product_id=$product_id");
    exit();
}

$stmt = $mysqli->prepare("
    SELECT messages.*, users.username FROM messages 
    JOIN users ON messages.sender_id = users.id
    WHERE (sender_id = ? AND receiver_id = ? OR sender_id = ? AND receiver_id = ?) 
    AND product_id = ?
    ORDER BY created_at ASC
");
$stmt->bind_param('iiiii', $user_id, $receiver_id, $receiver_id, $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Чат</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .chat-container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .chat-header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .chat-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }
        .message {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            word-wrap: break-word;
        }
        .sent {
            background-color: #dcf8c6;
            align-self: flex-end;
        }
        .received {
            background-color: #f1f0f0;
            align-self: flex-start;
        }
        .chat-form {
            display: flex;
        }
        .chat-form textarea {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <img src="<?= htmlspecialchars($product['product_image']) ?>" alt="Фото товара">
            <div>
                <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                <p>Продавец: <?= htmlspecialchars($product['owner_name']) ?></p>
            </div>
        </div>
        
        <div class="chat-box" id="chatBox">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= ($msg['sender_id'] == $user_id) ? 'sent' : 'received' ?>">
                    <strong><?= htmlspecialchars($msg['username']) ?>:</strong>
                    <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    <small><?= $msg['created_at'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="chat-form" action="chat.php?product_id=<?= $product_id ?>&receiver_id=<?= $receiver_id ?>" method="POST">
            <textarea name="message" required></textarea>
            <button type="submit">Отправить</button>
        </form>
        <p><a href="index.php">Главная страница</a></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</body>
</html>
