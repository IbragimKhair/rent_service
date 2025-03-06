<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Ошибка: Пользователь не авторизован.");
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;

if (!$receiver_id || !$product_id) {
    die("Ошибка: Неверные данные запроса.");
}

if (!empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param('iiis', $user_id, $receiver_id, $product_id, $message);
    $stmt->execute();
    $stmt->close();
}


header("Location: chat.php?receiver_id=$receiver_id&product_id=$product_id");
exit();
