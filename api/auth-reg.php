<?php
include __DIR__ . '/../system/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = false;
    $response = array('success' => false, 'message' => '');

    $secret = '6LdJx-MpAAAAAPvGdlie9esRy9w_rp_ehYPO1z2x';
    if (isset($_POST['g-recaptcha-response'])) {
        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
        $out = curl_exec($curl);
        curl_close($curl);
        
        $out = json_decode($out);
        if ($out->success !== true) {
            $error = true;
            $response['message'] = 'Ошибка в проверке reCAPTCHA.';
        }
    } else {
        $error = true;
        $response['message'] = 'Пожалуйста, заполните reCAPTCHA.';
    }

    if (!$error) {
        $login = $_POST['login'];
        $password = $_POST['password'];
        $password_hash = hash('sha256', $password);

        if (empty($login) || empty($password)) {
            $response['message'] = 'Пожалуйста, введите логин и пароль.';
        } else {
            $sql_check_user = "SELECT id FROM users WHERE nickname = ?";
            if ($stmt = $conn->prepare($sql_check_user)) {
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    $response['message'] = 'Пользователь с таким логином уже существует.';
                } else {
                    $sql_register_user = "INSERT INTO users (nickname, password, role, expiration_date) VALUES (?, ?, 'default', NULL)";
                    if ($stmt_register = $conn->prepare($sql_register_user)) {
                        $stmt_register->bind_param("ss", $login, $password_hash);
                        if ($stmt_register->execute()) {
                            session_start();
                            $_SESSION['login'] = $login;
                            $response['success'] = true;
                            $response['message'] = 'Регистрация успешна.';
                        } else {
                            $response['message'] = 'Что-то пошло не так. Пожалуйста, попробуйте еще раз.';
                        }
                        $stmt_register->close();
                    }
                }
                $stmt->close();
            }
        }
    }

    $conn->close();
    echo json_encode($response);
}
?>