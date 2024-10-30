<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST['product'];
    $amount = $_POST['amount'];

    $payment_number = time();
    $merchant_id = '111';
    $merchant_secret = '111';
    $currency = 'RUB';

    $sign = hash('sha256', implode(':', [$merchant_id, $amount, $currency, $merchant_secret, $payment_number]));

    ?>
    <form id="paymentForm" action='https://aaio.so/merchant/pay' method='POST'>
        <input type='hidden' name='merchant_id' value='<?php echo $merchant_id; ?>'>
        <input type='hidden' name='amount' value='<?php echo $amount; ?>'>
        <input type='hidden' name='currency' value='RUB'>
        <input type='hidden' name='order_id' value='<?php echo $payment_number; ?>'>
        <input type='hidden' name='sign' value='<?php echo $sign; ?>'>
        <input type='hidden' name='us_product' value='<?php echo $product; ?>'>
        <input type='hidden' name='lang' value='ru'>
        <input type='hidden' name='us_login' value='<?php echo $_SESSION['login']; ?>'>
    </form>
    <script>
        document.getElementById('paymentForm').submit();
    </script>
    <?php
} else {
    die('Invalid request');
}
?>