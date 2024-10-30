<?php
include __DIR__ . '/../system/db_config.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql_select_users = "SELECT * FROM users";
$result = $conn->query($sql_select_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель: Управление пользователями</title>
</head>
<body>
    <h2>Админ-панель: Управление пользователями</h2>
    <form action="admin_actions.php" method="post">
        <button type="submit" name="global_action" value="add_one_day">Добавить 1 день всем пользователям с активной подпиской</button>
        <input type="text" name="custom_days" placeholder="Введите количество дней">
        <button type="submit" name="global_action" value="add_custom_days">Добавить кастомное количество дней каждому пользователю с подпиской</button>
    </form>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Роль</th>
            <th>Дата окончания подписки</th>
            <th>Discord id</th>
            <th>Забанен</th>
            <th>Причина бана</th>
            <th>Действия</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $user_id = $row['id'];
                $expiration_date = $row['expiration_date'];
                $banned = $row['banned'];
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["nickname"] . "</td>";
                echo "<td>" . $row["role"] . "</td>";
                echo "<td>" . $row["expiration_date"] . "</td>";
                echo "<td>" . $row["discord_id"] . "</td>";
                echo "<td>" . $row["banned"] . "</td>";
                echo "<td>" . $row["reason"] . "</td>";
                echo "<td>";
                if ($banned === 'Да') {
                    echo "<form method='post' action='admin_actions.php'>";
                    echo "<input type='hidden' name='user_id' value='$user_id'>";
                    echo "<button type='submit' name='unban'>Разбанить</button>";
                    echo "</form>";
                } elseif ($expiration_date === NULL) {
                    echo "<form method='post' action='admin_actions.php'>";
                    echo "<input type='hidden' name='user_id' value='$user_id'>";
                    echo "<button type='submit' name='add_30_days'>Выдать саб 30 дней</button>";
                    echo "<button type='submit' name='add_90_days'>Выдать саб 90 дней</button>";
                    echo "<button type='submit' name='add_forever_subscription'>Выдать саб навсегда</button>";
                    echo "<button type='submit' name='delete_user'>Del user</button>";
                    echo "<input type='text' name='ds_id' placeholder='Дискорд ид'>";
                    echo "<button type='submit' name='set_ds_id'>Установить дискорд ид</button>";
                    echo "<input type='text' name='ban_reason' placeholder='Причина бана'>";
                    echo "<button type='submit' name='ban'>Забанить</button>";
                    echo "</form>";
                } else {
                    echo "<form action='admin_actions.php' method='post'>";
                    echo "<input type='hidden' name='user_id' value='" . $row["id"] . "'>";
                    echo "<input type='text' name='custom_days' placeholder='Введите колво дней'>";
                    echo "<button type='submit' name='add_custom_days'>Добавить кастом колво дней</button>";
                    echo "<input type='text' name='custom_role' placeholder='Введите кастом роль'>";
                    echo "<button type='submit' name='set_custom_role'>Поставить кастом роль</button>";
                    echo "<button type='submit' name='revoke_subscription'>Забрать саб</button>";
                    echo "<button type='submit' name='delete_user'>Del user</button>";
                    echo "<input type='text' name='ds_id' placeholder='Дискорд ид'>";
                    echo "<button type='submit' name='set_ds_id'>Установить дискорд ид</button>";
                    echo "<button type='submit' name='reset_hwid'>Сбросить хвид</button>";
                    echo "<input type='text' name='ban_reason' placeholder='Причина бана'>";
                    echo "<button type='submit' name='ban'>Забанить</button>";
                    echo "</form>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Нет данных о пользователях</td></tr>";
        }
        ?>
    </table>
    <span style="margin-bottom: 100px;"></span>
    <a href="https://neverclient.wtf/logout.php">Выйти из аккаунта</a>
</body>
</html>