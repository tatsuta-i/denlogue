<link rel="stylesheet" href="register.css">
<?php
//require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

require_once('connect.php'); //データベースにアクセスするphp

// セッションからログイン情報を得る
$user_id = $_SESSION['userid'];


// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

        // 3. エラー処理
        try {
            //$pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $dbh = db_connect(); //データベースに接続
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // DBの命令文
	    $p_id = $_GET["id"];
            $stmt = $dbh->prepare('SELECT good FROM post WHERE post_num = :p_id');
            $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
            $stmt->execute();

	    foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $message){
		$g_num = $message["good"];
		$g_num += 1;
	    }

            $stmt = null;

            $stmt = $dbh->prepare('UPDATE post SET good = :g_num  WHERE post_num = :p_id');
            $stmt->bindValue(':g_num', $g_num, PDO::PARAM_STR);
            $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = null;
            $dbh = null;
    
            // 処理が終わったら自動的にtimeline.phpに戻る
            if($dbh == null){
                header('Location: timeline.php');
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }

?>