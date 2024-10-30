<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}
include __DIR__ . '/../system/db_config.php';

$key = 'SRbesrASRBbrsa';

$merchant_id = $_REQUEST['merchant_id'];
$merchant_secret = '111';

$currency = 'RUB';
$order_id = $_REQUEST['MERCHANT_ORDER_ID'];
$amount = $_REQUEST['AMOUNT'];
$payment_number = $_REQUEST['MERCHANT_ORDER_ID'];
$intid = $_REQUEST['intid'];
$p_email = $_REQUEST['P_EMAIL'];
$cur_id = $_REQUEST['CUR_ID'];
$payer_account = $_REQUEST['payer_account'];
$product = $_REQUEST['us_product'];
$user_login = $_REQUEST['us_login'];

$sign = $_REQUEST['SIGN'];
$calculated_sign = md5($merchant_id . ':' . $amount . ':' . $merchant_secret . ':' . $payment_number);

$log_message = "--------------------------------\n";
$log_message .= "Payment notification received:\n";
$log_message .= "Order ID: $order_id\n";
$log_message .= "Amount: $amount\n";
$log_message .= "Int ID: $intid\n";
$log_message .= "Email: $p_email\n";
$log_message .= "Currency ID: $cur_id\n";
$log_message .= "Payer Account: $payer_account\n";
$log_message .= "Product: $product\n";
$log_message .= "User Login: $user_login\n";
$log_message .= "(original) Sign: $sign\n";
$log_message .= "(generated) Sign: $calculated_sign";

$amount_5_percent = $amount * 0.05;

$log_message .= "\n5% от суммы : $amount_5_percent";

$webhook_url = 'https://discord.com/api/webhooks/1301078506448486450/sfjBXsQgil2tQk_IK8Mb7-DYStRtUPzQzDypMVdPZ6EIRHSeF8V48IeBrP3rkkMsdSBW';

$discord_data = [
    'content' => $log_message
];

$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discord_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$discord_response = curl_exec($ch);
curl_close($ch);

if ($sign !== $calculated_sign) {
    die('Invalid sign');
}

if ($product === '30_days') {
    $postFields = ['nickname' => $user_login, 'add_30_days' => '1', 'key' => $key];
} elseif ($product === '180_days') {
    $postFields = ['nickname' => $user_login, 'add_180_days' => '1', 'key' => $key];
} elseif ($product === 'forever') {
    $postFields = ['nickname' => $user_login, 'add_forever_subscription' => '1', 'key' => $key];
} elseif ($product === 'hwid_reset') {
    $postFields = ['nickname' => $user_login, 'hwid_reset' => '1', 'key' => $key];
} else {
    die('Unknown product: ' . htmlspecialchars($product));
}

$ch = curl_init('https://boostanull.fun/api/actions.php');

if ($ch === false) {
    die('Failed to initialize cURL');
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die('cURL error: ' . htmlspecialchars($error));
}

curl_close($ch);

echo 'YES';
?>