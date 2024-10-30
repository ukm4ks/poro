<?php
include __DIR__ . '/../system/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["nickname"]) && isset($_POST['key'])) {
        $nickname = $_POST["nickname"];
        $key = $_POST['key'];

        if($key !== 'SRbesrASRBbrsa') {
            echo "Неверный ключ.";
            exit;
        }
        
        $sql_select_expiration_date = "SELECT expiration_date FROM users WHERE nickname = ?";
        $stmt = $conn->prepare($sql_select_expiration_date);
        $stmt->bind_param("s", $nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_expiration_date = $row["expiration_date"];
            $sql_select_role = "SELECT role FROM users WHERE nickname = ?";
            $stmt = $conn->prepare($sql_select_role);
            $stmt->bind_param("s", $nickname);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $role = $row["role"];
                
                if (isset($_POST["add_30_days"])) {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $sql_update_subscription = "UPDATE users SET expiration_date = ? WHERE nickname = ?";
                    $stmt = $conn->prepare($sql_update_subscription);
                    $stmt->bind_param("ss", $expiration_date, $nickname);
                    $stmt->execute();
                    
                    if ($role === 'default') {
                        $sql_update_role = "UPDATE users SET role = 'user' WHERE nickname = ?";
                        $stmt = $conn->prepare($sql_update_role);
                        $stmt->bind_param("s", $nickname);
                        $stmt->execute();
                    }
                } elseif (isset($_POST["add_90_days"])) {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+90 days'));
                    $sql_update_subscription = "UPDATE users SET expiration_date = ? WHERE nickname = ?";
                    $stmt = $conn->prepare($sql_update_subscription);
                    $stmt->bind_param("ss", $expiration_date, $nickname);
                    $stmt->execute();
                    
                    if ($role === 'default') {
                        $sql_update_role = "UPDATE users SET role = 'user' WHERE nickname = ?";
                        $stmt = $conn->prepare($sql_update_role);
                        $stmt->bind_param("s", $nickname);
                        $stmt->execute();
                    }
                } elseif (isset($_POST["add_180_days"])) {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+180 days'));
                    $sql_update_subscription = "UPDATE users SET expiration_date = ? WHERE nickname = ?";
                    $stmt = $conn->prepare($sql_update_subscription);
                    $stmt->bind_param("ss", $expiration_date, $nickname);
                    $stmt->execute();
                    
                    if ($role === 'default') {
                        $sql_update_role = "UPDATE users SET role = 'user' WHERE nickname = ?";
                        $stmt = $conn->prepare($sql_update_role);
                        $stmt->bind_param("s", $nickname);
                        $stmt->execute();
                    }
                } elseif (isset($_POST["hwid_reset"])) {
                    $sql_hwid_reset = "UPDATE users SET hwid = NULL WHERE nickname = ?";
                    $stmt = $conn->prepare($sql_hwid_reset);
                    $stmt->bind_param("s", $nickname);
                    $stmt->execute();
                } elseif (isset($_POST["add_forever_subscription"])) {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+10000 days'));
                    $sql_update_subscription = "UPDATE users SET expiration_date = ? WHERE nickname = ?";
                    $stmt = $conn->prepare($sql_update_subscription);
                    $stmt->bind_param("ss", $expiration_date, $nickname);
                    $stmt->execute();
                    
                    if ($role === 'default') {
                        $sql_update_role = "UPDATE users SET role = 'user' WHERE nickname = ?";
                        $stmt = $conn->prepare($sql_update_role);
                        $stmt->bind_param("s", $nickname);
                        $stmt->execute();
                    }
                }
            } else {
                echo "Роль пользователя не найдена.";
            }
        } else {
            echo "Дата истечения подписки не найдена.";
        }
    } else {
        echo "Не переданы параметры.";
    }
} else {
    echo "Метод запроса должен быть POST.";
}
?>