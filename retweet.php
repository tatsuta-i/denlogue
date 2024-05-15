<?php
require_once('connect.php'); //データベースにアクセスするphp

// セッション開始
session_start();

// postがある場合
if(isset($_POST['retweet'])){
    $p_id = $_POST['retweet'];
    $user_id = $_SESSION['userid'];

    try{

        $dbh = db_connect(); //データベースに接続
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //例外処理を投げるようにする

        // goodテーブルから投稿IDとユーザーIDが一致したレコードを取得するSQL文
        $sql = 'SELECT * FROM post WHERE ret_num = :p_id AND user_id = :u_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $_SESSION['userid']);
        $stmt -> execute();
        $resultCount = $stmt->rowCount();
        // レコードが1件でもある場合
 
        if(!empty($resultCount)){
            // レコードを削除する
            set();
            // delete();
            $stmt = $dbh->prepare('DELETE FROM post WHERE ret_num = :p_id AND user_id = :u_id');
            $stmt->bindValue(':p_id', $p_id);
            $stmt->bindValue(':u_id', $user_id);
            $stmt->execute();
        }else{
            // レコードを挿入する
            noti_re($p_id);
            $stmt = $dbh->prepare('INSERT INTO post(user_id, date, ret_num) values(:user_id, now(), :ret)');
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':ret', $p_id, PDO::PARAM_STR);
            $stmt->execute();
            // $sql = 'INSERT INTO retweet (post_id, user_id) VALUES (:p_id, :u_id)';
        }
        
        // いいね数を表示 テストコード
        $cnt = count(getRetweet($p_id));
        $sql = 'UPDATE post SET retweet = :cnt  WHERE post_num = :p_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':cnt', $cnt);
        $stmt->bindValue(':p_id', $p_id);
        $stmt -> execute();
        
        if(isset($_POST['del_ret'])){
            header('Location: timeline.php');
        }
    }catch(Exception $e){
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
}

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
function set(){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SET FOREIGN_KEY_CHECKS = 0";
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
}

function delete(){
    $dbh = connectDB();
    $post_num = $_POST['retweet'];
    // DBの命令文
    $sql = "DELETE FROM post
            WHERE ret_num = :post_num";
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':post_num', $post_num, PDO::PARAM_STR);
    $stmt -> execute();
}

function noti_re($p_id){
    $user_id = $_SESSION['userid'];
    $dbh = connectDB();
    $stmt = $dbh->prepare('SELECT user_id from post where post_num = :p_id');
    $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
    $stmt->execute();
    $to_id = $stmt->fetch();
    $stmt = $dbh->prepare('UPDATE account set notice = notice + 1 where user_id = :user_id');
    $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
    $stmt->execute();
    $stmt = $dbh->prepare('INSERT INTO notice(user_id, send_id, flag, post_num) values(:user_id, :s_id, 1, :p_num)');
    $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
    $stmt->bindValue(':s_id', $user_id, PDO::PARAM_STR);
    $stmt->bindValue(':p_num', $p_id, PDO::PARAM_STR);
    $stmt->execute();
}

// いいね数を獲得する
function getRetweet($p_id){
	try {
		$dbh = connectDB();
        $sql = 'SELECT * FROM post WHERE ret_num = :p_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
		$stmt -> execute();
		if($stmt){
            return $stmt->fetchAll();
		}else{
			return false;
		}
	} catch (Exception $e) {
        echo $e->getMessage();
	}
}

?>