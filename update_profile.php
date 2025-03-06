<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_username'])) {
    $username = trim($_POST['username']);
    if (!empty($username)) {
        $stmt = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param('si', $username, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $mysqli->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_phone'])) {
    $phone = trim($_POST['phone']);
    if (!empty($phone)) {
        $stmt = $mysqli->prepare("UPDATE users SET phone = ? WHERE id = ?");
        $stmt->bind_param('si', $phone, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_bio'])) {
    $bio = trim($_POST['bio']);
    $stmt = $mysqli->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->bind_param('si', $bio, $user_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $uploadedFile = $_FILES['profile_picture'];

    $allowedExtensions = ['png', 'jpg', 'jpeg'];
    $fileExtension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = 'profile_' . $user_id . '.' . $fileExtension;
        $uploadPath = 'uploads/' . $newFileName;

        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
            $stmt = $mysqli->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param('si', $newFileName, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Ошибка при загрузке файла.";
        }
    } else {
        $_SESSION['error_message'] = "Недопустимый формат файла. Доступные форматы: PNG, JPG, JPEG.";
    }
}

header('Location: profile.php');
exit();
?>
