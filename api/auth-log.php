<?php
include __DIR__ . '/../system/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = false;
    $response = array('error' => false, 'message' => '');

    $secret = '6LdJx-MpAAAAAPvGdlie9esRy9w_rp_ehYPO1z2x';
    if (isset($_POST['g-recaptcha-response'])) {
        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
        $out = curl_exec($curl);
        curl_close($curl);
        
        $out = json_decode($out);
        if (!$out->success) {
            $error = true;
            $response['error'] = true;
            $response['message'] = 'Ошибка в проверке reCAPTCHA.';
        }
    } else {
        $error = true;
        $response['error'] = true;
        $response['message'] = 'Пожалуйста, заполните reCAPTCHA.';
    }

    if (!$error) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password_hash = hash('sha256', $password);

        $sql_check_user = "SELECT * FROM users WHERE nickname = ? AND password = ?";
    
        if ($stmt = $conn->prepare($sql_check_user)) {
            $stmt->bind_param("ss", $username, $password_hash);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                session_start();
                $_SESSION['login'] = $username;
                $response['message'] = 'Успешная авторизация.';
            } else {
                $response['error'] = true;
                $response['message'] = 'Неправильный логин или пароль.';
            }
        }
    }

    $conn->close();
    echo json_encode($response);
}
?>