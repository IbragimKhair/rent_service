<?php
$mysqli = new mysqli('localhost', 'root', '', 'rent_service');

if ($mysqli->connect_error) {
    die('Ошибка подключения к базе данных: ' . $mysqli->connect_error);
}
?>
