<link rel="stylesheet" href="register.css">
<?php

// セッション開始
session_start();

// ログインしてるかどうかの確認をする処理
$no_login_url = 'login.php';
header("Location: {$no_login_url}"); // login.php に戻される
exit;
?>
