<link rel="stylesheet" href="register.css">
<?php
//require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

// ログインしてるかどうかの確認をする処理
if(!isset($_SESSION['userid'])) { // ログインしていなかったら
    $no_login_url = 'login.php';
    header("Location: {$no_login_url}"); // login.php に戻される
    exit;
}

$signUpMessage = "";

?>
<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
            <link rel="stylesheet" href="register.css"> <!-- cssとの連携 -->
    </head>
    <body>
        <h1>登録完了画面</h1>
	<p>登録が完了しました。あなたの登録IDは<?php echo $_SESSION['loginid']; ?>です。</p>
	<p>パスワードは<?php echo $_SESSION['password']; ?>です。</p>
        <form action="timeline.php">
        <button>OK</button>
        </form>
    </body>
</html>
