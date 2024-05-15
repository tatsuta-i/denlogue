<?php
// セッション開始
session_start();

// ログインしてるかどうかの確認をする処理
if(!isset($_SESSION['userid'])) { // ログインしていなかったら
    $no_login_url = 'login.php';
    header("Location: {$no_login_url}"); // login.php に戻される
    exit;
}
require_once('connect.php'); //データベースにアクセスするphp
$user_id = $_SESSION["userid"];
$page_id = $_GET["userid"];

if($user_id == $page_id){
    header('Location: mypage.php');
} else {
    $_SESSION['page'] = $page_id;
    header('Location: profile.php');
}
?>
