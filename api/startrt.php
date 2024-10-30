<?php
include __DIR__ . '/../system/db_config.php';

$sql_starts = "SELECT starts FROM other";
$result_starts = $conn->query($sql_starts);

if ($result_starts && $result_starts->num_rows > 0) {
    $row_starts = $result_starts->fetch_assoc();
    $currentStarts = $row_starts['starts'];
    
    $newStarts = $currentStarts + 1;
    
    $sql_update_starts = "UPDATE other SET starts = $newStarts";
    if ($conn->query($sql_update_starts) === TRUE) {
        echo "Значение starts успешно обновлено";
    } else {
        echo "Ошибка при обновлении значения starts: " . $conn->error;
    }
} else {
    echo "Нет данных о starts";
}

$conn->close();
?>