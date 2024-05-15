<?php
//共通変数・関数ファイルを読込み
//require('timeline.php');
//require('function.php');
require_once('connect.php'); //データベースにアクセスするphp

// セッション開始
session_start();

// postがある場合
if(isset($_POST['good_post'])){
    $p_id = $_POST['good_post'];

    try{

        $dbh = db_connect(); //データベースに接続
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //例外処理を投げるようにする

        // goodテーブルから投稿IDとユーザーIDが一致したレコードを取得するSQL文
        $sql = 'SELECT * FROM Good WHERE post_num = :p_id AND user_id = :u_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $_SESSION['userid']);
        $stmt -> execute();
        $resultCount = $stmt->rowCount();
        // レコードが1件でもある場合
 
        if(!empty($resultCount)){
            // レコードを削除する
            $sql = 'DELETE FROM Good WHERE post_num = :p_id AND user_id = :u_id';
            //$data = array(':p_id' => $p_id, ':u_id' => $_SESSION['userid']);
        }else{
            // レコードを挿入する
            $sql = 'INSERT INTO Good (post_num, user_id) VALUES (:p_id, :u_id)';
            noti_good($p_id);
            //$data = array(':p_id' => $p_id, ':u_id' => $_SESSION['userid']);
        }
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $_SESSION['userid']);
        $stmt -> execute();
        
        // いいね数を表示 テストコード
        $cnt = count(getGood($p_id));
        echo $cnt;
        $sql = 'UPDATE post SET good = :cnt  WHERE post_num = :p_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':cnt', $cnt);
        $stmt->bindValue(':p_id', $p_id);
        $stmt -> execute();
    }catch(Exception $e){
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
}

function noti_good($p_id){
    $user_id = $_SESSION['userid'];
    $dbh = db_Connect();
    $stmt = $dbh->prepare('SELECT user_id from post where post_num = :p_id');
    $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
    $stmt->execute();
    $to_id = $stmt->fetch();
    $stmt = $dbh->prepare('UPDATE account set notice = notice + 1 where user_id = :user_id;');
    $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
    $stmt->execute();
    $stmt = $dbh->prepare('INSERT INTO notice(user_id, send_id, flag, post_num) values(:user_id, :s_id, 0, :p_num)');
    $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
    $stmt->bindValue(':s_id', $user_id, PDO::PARAM_STR);
    $stmt->bindValue(':p_num', $p_id, PDO::PARAM_STR);
    $stmt->execute();
}

// いいね数を獲得する
function getGood($p_id){
	try {
		$dbh = db_Connect();
        $sql = 'SELECT * FROM Good WHERE post_num = :p_id';
        $stmt = $dbh -> prepare($sql);
        //$stmt = array(':p_id' => $p_id);
        $stmt->bindValue(':p_id', $p_id);
		// クエリ実行
		$stmt -> execute();

		if($stmt){
            return $stmt->fetchAll();
		}else{
			return false;
		}
	} catch (Exception $e) {
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
	}
}

?>