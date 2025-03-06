<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $mysqli->prepare("UPDATE products SET status = 'deleted' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: moderate_products.php');
exit();
