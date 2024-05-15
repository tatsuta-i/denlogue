<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
$_SESSION['page'] = $_GET["userid"];

// DB接続する関数
function connectDB(){
    try {
        $dbh = db_connect(); //データベースに接続
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //例外処理を投げるようにする
        return $dbh;
    } catch (PDOException $e) {
        $errorMessage = 'データベースエラー';
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        // echo $e->getMessage();
    }
}

function select_name(){
    $dbh = connectDB();
    $page_id = $_SESSION["page"];
    $prof = "SELECT name
    	    FROM account
    	    WHERE :userid = user_id";
    $st = $dbh -> prepare($prof);
    $st -> bindValue(':userid', $page_id);
    $st -> execute();
    return $st;
}
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>プロフィール</title>
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src = "script.js"></script>
    </head>

    <body>
    <?php require('side_bar.php'); ?>

    <?php
        $st = select_name();
        $result = $st->fetch(PDO::FETCH_ASSOC);
        $NamePage = $result['name']."のマイページ";
        require('header.php');
    ?>
    <main>
        <a href="#top" id="page_top"><span class ="fa fa-angle-up"></span></a>
        <?php include('read_prof.php'); ?>
        </main>
    </body>
</html>
