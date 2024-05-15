<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');

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
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>フォローTL</title>
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src = "script.js"></script>
    </head>
    <body>
        <!-- サイドバーの表示をside_bar.phpに投げる -->
        <?php require('side_bar.php'); ?>

        <!-- ヘッダー処理をheader.phpに投げる -->
        <?php
            $NamePage = "フォローTL"; // ヘッダーに表示する現在のページ名
            require('header.php');
        ?>

        <main>
            <a href="#top" id="page_top"><span class ="fa fa-angle-up"></span></a>
            <?php include('read_follow.php');?>
        </main>
    </body>
</html>