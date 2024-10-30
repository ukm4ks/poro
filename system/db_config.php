<?php
$db_config = array(
    'host' => '123',
    'username' => '123',
    'password' => '123',
    'database' => '123'
);

$conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Ошибка соединения с базой данных: " . $conn->connect_error);
}
?>