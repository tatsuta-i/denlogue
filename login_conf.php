<?php
// ログインの確認をするファイル

// セッション開始
session_start();
// ログインしてるかどうかの確認をする処理
if(!isset($_SESSION['userid'])) { // ログインしていなかったら
    $no_login_url = 'login.php';
    header("Location: {$no_login_url}"); // login.php に戻される
    exit;
}
?>