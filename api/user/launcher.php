<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: https://neverclient.wtf/cabinet.php");
    exit();
}

$file_url = 'https://dolphin.rent/neverclient/Loader.exe';
$downloads_dir = 'downloads/';

$random_part = bin2hex(random_bytes(5));
$new_file_name = $random_part . '.exe';
$save_to = $downloads_dir . $new_file_name;

$file_contents = @file_get_contents($file_url);
if ($file_contents !== false) {
    if (file_put_contents($save_to, $file_contents) !== false) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($save_to) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($save_to));
        
        readfile($save_to);
        unlink($save_to);
        
        header("Location: https://neverclient.wtf/cabinet.php");
        exit();
    } else {
        echo "Error: Unable to save file";
    }
} else {
    echo "Error: Unable to download file";
}
?>