<?php
session_start();
require_once('connect.php'); //データベースにアクセスするphp

$stmt = select();
$image = $stmt->fetch();
header("Content-Type: image/jpeg");
echo $image['icon'];

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

// DBから投稿内容を取得する関数
function select_icon(){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SELECT image
	    FROM account
	    WHERE user_id = ". $id;
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    $image = $stmt->fetch();
    header("Content-Type: image/jpeg");

    return $image['image'];
}
?>