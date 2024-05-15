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
        <title>タイムライン</title>
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css"> -->
        <!---<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
        <script src = "script.js"></script>
    </head>

    <body>
        <!-- サイドバーの表示をside_bar.phpに投げる -->
        <?php require('side_bar.php'); ?>

        <!-- ヘッダー処理をheader.phpに投げる -->
        <?php
            $NamePage = "タイムライン"; // ヘッダーに表示する現在のページ名
            require('header.php');
        ?>

         <!--<main>-->
        <main>
            <a href="#top" id="page_top"><span class ="fa fa-angle-up"></span></a>


           <!-- タイムラインのフォーム -->
           <?php include('read.php');
               // read.php にタイムラインの処理を投げる
           ?>
       </main>
   </body>
</html>

