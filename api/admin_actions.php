<?php
include __DIR__ . '/../system/db_config.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["user_id"])) {
        $user_id = $_POST["user_id"];
        $login = $_SESSION['login'];

        $sql_select_expiration_date = "SELECT expiration_date FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql_select_expiration_date);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_expiration_date = $row["expiration_date"];
        $stmt->close();

        $sql_select_role = "SELECT role FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql_select_role);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $role = $row["role"];
        $stmt->close();

        $sql_select_role_admin = "SELECT role FROM users WHERE nickname = ?";
        $stmt = $conn->prepare($sql_select_role_admin);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $role_admin = $row["role"];
        $stmt->close();

        if ($role_admin !== 'admin') {
            echo 'Алё, ты не админ, петушара';
            exit();
        }

        if (isset($_POST["add_30_days"])) {
            $expiration_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        } elseif (isset($_POST["add_90_days"])) {
            $expiration_date = date('Y-m-d H:i:s', strtotime('+90 days'));
        } elseif (isset($_POST["add_forever_subscription"])) {
            $expiration_date = date('Y-m-d H:i:s', strtotime('+10000 days'));
        } elseif (isset($_POST["add_custom_days"])) {
            $custom_days = $_POST["custom_days"];
            $expiration_date = date('Y-m-d H:i:s', strtotime("+$custom_days days", strtotime($current_expiration_date)));
        } elseif (isset($_POST["set_custom_role"])) {
            $custom_role = $_POST["custom_role"];
            $sql_update_role = "UPDATE users SET role = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update_role);
            $stmt->bind_param("si", $custom_role, $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST["ban"])) {
            $reason = $_POST["ban_reason"];
            $sql_ban = "UPDATE users SET banned = 'Да', reason = ?, role = 'default', expiration_date = NULL, hwid = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql_ban);
            $stmt->bind_param("si", $reason, $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST["unban"])) {
            $sql_unban = "UPDATE users SET banned = 'Нет', reason = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql_unban);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST["reset_hwid"])) {
            $sql_reset_hwid = "UPDATE users SET hwid = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql_reset_hwid);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST["set_ds_id"])) {
            $ds_id = $_POST["ds_id"];
            $sql_set_ds_id = "UPDATE users SET discord_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_set_ds_id);
            $stmt->bind_param("si", $ds_id, $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST["revoke_subscription"])) {
            $sql_revoke_subscription = "UPDATE users SET expiration_date = NULL, role = 'default' WHERE id = ?";
            $stmt = $conn->prepare($sql_revoke_subscription);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            if ($role === 'admin') {
                $sql_restore_admin = "UPDATE users SET role = 'admin' WHERE id = ?";
                $stmt = $conn->prepare($sql_restore_admin);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
            }
            $stmt->close();
        } elseif (isset($_POST["delete_user"])) {
            $sql_delete_user = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql_delete_user);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST["global_action"] == "add_one_day") {
            $sql_add_one_day = "UPDATE users SET expiration_date = DATE_ADD(expiration_date, INTERVAL 1 DAY) WHERE expiration_date > ?";
            $stmt = $conn->prepare($sql_add_one_day);
            $stmt->bind_param("s", $current_date);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST["global_action"] == "add_custom_days") {
            $custom_days = $_POST["custom_days"];
            $sql_add_custom_days = "UPDATE users SET expiration_date = DATE_ADD(expiration_date, INTERVAL ? DAY) WHERE expiration_date > ?";
            $stmt = $conn->prepare($sql_add_custom_days);
            $stmt->bind_param("is", $custom_days, $current_date);
            $stmt->execute();
            $stmt->close();
        }

        if (isset($expiration_date)) {
            $sql_update_subscription = "UPDATE users SET expiration_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update_subscription);
            $stmt->bind_param("si", $expiration_date, $user_id);
            $stmt->execute();
            $stmt->close();
            if ($role === 'default') {
                $sql_update_role = "UPDATE users SET role = 'user' WHERE id = ?";
                $stmt = $conn->prepare($sql_update_role);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        header("Location: users_control.php");
        exit();
    } elseif (isset($_POST["global_action"])) {
        $current_date = date('Y-m-d H:i:s');

        if ($_POST["global_action"] == "add_one_day") {
            $sql_add_one_day = "UPDATE users SET expiration_date = DATE_ADD(expiration_date, INTERVAL 1 DAY) WHERE expiration_date > ?";
            $stmt = $conn->prepare($sql_add_one_day);
            $stmt->bind_param("s", $current_date);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST["global_action"] == "add_custom_days") {
            $custom_days = $_POST["custom_days"];
            $sql_add_custom_days = "UPDATE users SET expiration_date = DATE_ADD(expiration_date, INTERVAL ? DAY) WHERE expiration_date > ?";
            $stmt = $conn->prepare($sql_add_custom_days);
            $stmt->bind_param("is", $custom_days, $current_date);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_control.php");
        exit();
    }
}
?>