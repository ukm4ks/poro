<?php
$db_config = [
    'host' => 'localhost',
    'user' => 'Never',
    'password' => '9h091KdqPHSctNWk',
    'database' => 'Never'
];

function check_expired_users() {
    global $db_config;

    $connection = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['database']);

    if ($connection->connect_error) {
        die("Failer, erro: " . $connection->connect_error);
    }

    $sql_get_expired_keys = "SELECT `nickname` FROM users WHERE expiration_date < NOW()";
    $result = $connection->query($sql_get_expired_keys);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expired_key = $row['nickname'];
            $sql_update_role = "UPDATE users SET `role` = 'default', `expiration_date` = NULL WHERE `nickname` = ?";
            $stmt = $connection->prepare($sql_update_role);
            $stmt->bind_param("s", $expired_key);
            $stmt->execute();
            $stmt->close();
        }
        echo "ПРОВЕРЕНО!";
    } else {
        echo "Нет истекших юзеров для обновления.";
    }

    $connection->close();
}

check_expired_users();
?>