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

select_set();
$stmt = select_ret();
foreach($stmt -> fetchAll(PDO::FETCH_ASSOC) as $message){
    del_ret($message['post_num']);
}
get_com();
update_post();
// del_com();
del_good();
del_notice();
del();

header('Location: timeline.php');

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
function select_set(){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SET FOREIGN_KEY_CHECKS = 0";
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
}

//リツイートから消す
function select_ret(){
    $dbh = connectDB();
    $post_num = $_POST['delete'];
    $sql = "SELECT post_num FROM post WHERE ret_num = :p_num";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':p_num', $post_num);
    $stmt->execute();

    return $stmt;
}

function del_notice(){
    $dbh = connectDB();
    $post_num = $_POST['delete'];
    $sql = "DELETE FROM notice WHERE post_num = :p_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':p_id', $post_num);
    $stmt->execute();
}

function del_ret($id){
    $dbh = connectDB();
    //$sql = "UPDATE post_tb SET retweet = NULL WHERE post_num = :id";
    $sql = "DELETE FROM post WHERE post_num = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    // $sql = "DELETE FROM retweet WHERE post_id = :p_id AND user_id = :u_id";
    // $stmt = $dbh->prepare($sql);
    // $stmt->bindValue(':p_id', $id);
    // $stmt->bindValue(':u_id', $_SESSION['userid']);
    // $stmt->execute();
}

function del(){
    $dbh = connectDB();
    $post_num = $_POST['delete'];
    // DBの命令文
    $sql = "DELETE FROM post
            WHERE post_num = :post_num";
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':post_num', $post_num, PDO::PARAM_STR);
    $stmt -> execute();
}

function del_com(){
    $dbh = connectDB();
    $post_num = $_POST['delete'];
    // DBの命令文
    $sql = "DELETE FROM post
            WHERE parent = :post_num";
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':post_num', $post_num, PDO::PARAM_STR);
    $stmt -> execute();
}

function update_post(){
    $dbh = connectDB();
    $p_id = $_POST['delete'];
    // DBの命令文
    $sql = 'SELECT post_num FROM post WHERE parent = :p_id';
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':p_id', $p_id);
    // クエリ実行
    $stmt -> execute();

    foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $child){
        $sql2 = "UPDATE post SET parent IS NULL WHERE post_num = :post_num";
        $stmts = $dbh -> prepare($sql2);
        $stmts->bindValue(':post_num', $child['post_num'], PDO::PARAM_STR);
        $stmts -> execute();
    }
}

function del_good(){
    $dbh = connectDB();
    $post_num = $_POST['delete'];
    // DBの命令文
    $sql = "DELETE FROM Good
            WHERE post_num = :post_num";
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':post_num', $post_num, PDO::PARAM_STR);
    $stmt -> execute();
}

// コメント数を獲得する
function get_com(){
    try {
        $dbh = db_Connect();
        $c_id = $_POST['delete'];
        $sql = 'SELECT parent FROM post WHERE post_num = :c_id';
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':c_id', $c_id);
        // クエリ実行
        $stmt -> execute();
        $parent = $stmt->fetch();

        if(isset($parent)){
            $p_id = $parent['parent'];
            $sql2 = 'SELECT post_num FROM post WHERE parent = :p_id';
            $stmts = $dbh -> prepare($sql2);
            $stmts->bindValue(':p_id', $p_id);
            // クエリ実行
            $stmts -> execute();

            $cnt = count($stmts->fetchAll()) - 1;
            $sql = 'UPDATE post SET comment = :cnt WHERE post_num = :p_id';
            $st_com = $dbh -> prepare($sql);
            $st_com->bindValue(':cnt', $cnt);
            $st_com->bindValue(':p_id', $p_id);
            $st_com -> execute();
        }
    } catch (Exception $e) {
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
}
?>