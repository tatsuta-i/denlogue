<?php
// セッション開始
session_start();

// ログインしてるかどうかの確認をする処理
if(!isset($_SESSION['userid'])) { // ログインしていなかったら
    $no_login_url = 'login.php';
    header("Location: {$no_login_url}"); // login.php に戻される
    exit;
}
require_once('connect.php'); //データベースにアクセスするphp

$stmt = select();
  // select関数で取得したDBデータを $message に格納 
  foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $message){
      // 取得したDBデータから何を表示するかを設定する
      // 添字はDBのカラムで指定
      echo $message['name'],":",$message['text'],":",$message['date'];
      echo nl2br("\n"); // 改行
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
  function select(){
      $dbh = connectDB();
      // DBの命令文
      $p_id = $_GET['id'];
      $sql = "SELECT *
              FROM account, post 
              WHERE account.user_id=post.user_id
              AND post_num = ". $p_id."
              ORDER BY date DESC"; 
      $stmt = $dbh -> prepare($sql);
      // 多分、postidを指定してあげて、whereすれば出てくる?該当のものが出てくる。つまり、postidをpostすればよい？
      //$stmt -> bindValue(':p_id', $p_id);
      $stmt -> execute();
      return $stmt;
  }
  // DBから投稿内容を取得(最新の1件)
?>