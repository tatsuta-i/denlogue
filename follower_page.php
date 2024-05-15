<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
require_once('connect.php'); //データベースにアクセスするphp
?>

<!-- <form name="readform" method="post" action = "syosai.php"> -->
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>フォロワー一覧</title>
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src = "script.js"></script>
    </head>

    <body>
        <!-- ここからPHPの処理 -->
        <?php
        $follow_id = $_GET["id"];
        require('side_bar.php');

        $st = select_name($follow_id);
        $result = $st->fetch(PDO::FETCH_ASSOC);
        $NamePage = $result['name']."のフォロワー一覧";
        require('header.php');

        echo "<main>";
            echo "<a href='#top' id='page_top'><i class ='fa fa-chevron-up'></i></a>";

            $stmt = select_follower($follow_id);
            echo "<div class = 'follow_margin'>";
            echo "<div class ='follow_border'>";
            // select関数で取得したDBデータを $message に格納
            foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $follow){
                echo "<form method='GET' action = 'profile.php'>";
                echo "<a href =\"profile.php?userid={$follow['user_id']}\" id='clear_sample'>"; // リンクを付与
                if($follow['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($follow['image']). "' width = '30' height = '30'>",$follow['name']."<font class = 'log-id'>  ",$follow['log_id']."</font></span> ";
                } else if($follow['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($follow['image']). "' width = '30' height = '30'>",$follow['name']."<font class = 'log-id'>  ",$follow['log_id']."</font></span> ";
                } else if($follow['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($follow['image']). "' width = '30' height = '30'>",$follow['name']."<font class = 'log-id'>  ",$follow['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$follow['name']."<font class = 'log-id'>  ",$follow['log_id']."</font></span> ";
                }
                echo "</a></form><HR>";
            }
            echo "</div></div>";

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

            function select_name($id){
                $dbh = connectDB();
                $prof = "SELECT name
                        FROM account
                        WHERE :userid = user_id";
                $st = $dbh -> prepare($prof);
                $st -> bindValue(':userid', $id);
                $st -> execute();
                return $st;
            }

            // DBから投稿内容を取得する関数
            function select_follower($id){
                $dbh = connectDB();
                $sql = 'SELECT account.user_id, account.log_id, account.name, account.image, account.dot FROM account, follow WHERE follow.follow = :id AND follow.follower = account.user_id';
                $stmt = $dbh -> prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt -> execute();

                return $stmt;
            }?>
        </main>
    </body>
</html>