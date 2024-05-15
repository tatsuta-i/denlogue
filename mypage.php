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
        <title>マイページ</title>
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src = "script.js"></script>
    </head>

    <body>
        <?php require('side_bar.php'); ?>

        <?php
            $NamePage = "マイページ"; // ヘッダーに表示する現在のページ名
            require('header.php');
        ?>
        <main>
            <a href="#top" id="page_top"><span class ="fa fa-angle-up"></span></a>
            <?php
                if(isset($_POST['submit'])) {
                    $file = $_FILES["image"]["tmp_name"];
                    if($file==""){
                        print("アイコンの変更が出来ませんでした。<BR>");
                    } else{
                        $imgdat = file_get_contents($file);
                        $dot = substr(strchr($_FILES['image']['name'], '.'), 1);
                        up_image($imgdat, $dot);
                    }
                }

                function up_image($imgdat, $dot){
                    $dbh = connectDB();
                    $user_id = $_SESSION["userid"];
                    $sql = "UPDATE account SET image = :imgdat, dot = :dot WHERE user_id = :user_id";
                    $stmt = $dbh -> prepare($sql);
                    $stmt->bindValue(':imgdat', $imgdat);
                    $stmt->bindValue(':dot', $dot);
                    $stmt->bindValue(':user_id', $user_id);
                    $stmt -> execute();
                }
            ?>
            <?php include('read_me.php'); ?>
        </main>
    </body>
</html>
