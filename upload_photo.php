<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

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

            header('Location: profile.php');
            exit();
        } else {
            echo 'Ошибка при загрузке файла.';
        }
    } else {
        echo 'Недопустимый формат файла. Доступные форматы: PNG, JPG, JPEG.';
    }
} else {
    echo 'Ошибка при загрузке фото.';
}
?>
