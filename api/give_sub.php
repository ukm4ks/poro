<?php
include __DIR__ . '/../system/db_config.php';

if(isset($_POST['nickname']) && isset($_POST['value']) && isset($_POST['key'])) {
    $nickname = $_POST['nickname'];
    $value = $_POST['value'];
    $key = $_POST['key'];

    if($key !== 'AEBarAsRSRnNnr') {
        echo "Неверный ключ.";
        exit;
    }

    $currentDateTime = date('Y-m-d H:i:s');

    $expirationDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' + ' . $value . ' days'));

    $stmt = $pdo->prepare("UPDATE users SET expiration_date = :expiration_date, role = 'user' WHERE nickname = :nickname");

    $stmt->execute(['expiration_date' => $expirationDateTime, 'nickname' => $nickname]);

    if($stmt->rowCount() > 0) {
        echo "Запись успешно обновлена.";
    } else {
        echo "Произошла ошибка при обновлении записи.";
    }
} else {
    echo "Не переданы все необходимые параметры.";
}
?>