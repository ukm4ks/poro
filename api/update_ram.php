<?php
include __DIR__ . '/../system/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ram']) && isset($_POST['userId'])) {

    $newRam = $_POST['ram'];
    $userId = $_POST['userId'];

    $sql_update_ram = "UPDATE users SET ram = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql_update_ram)) {
        $stmt->bind_param("ii", $newRam, $userId);
        if ($stmt->execute()) {
            echo "Оперативная память успешно обновлена!";
        } else {
            echo "Произошла ошибка при обновлении оперативной памяти: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Произошла ошибка при подготовке запроса: " . $conn->error;
    }
} else {
    echo "Произошла ошибка: данные не были переданы или это не POST запрос.";
}

$conn->close();
?>