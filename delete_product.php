<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $mysqli->prepare("SELECT user_id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') {
        $stmt = $mysqli->prepare("UPDATE products SET status = 'deleted' WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: products.php');
exit();
