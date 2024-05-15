<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
require_once('connect.php'); //データベースにアクセスするphp
?>

<!-- <form name="readform" method="post" action = "syosai.php"> -->

<!-- ここからPHPの処理 -->
<?php
select();

$page_id = $_SESSION['page'];
$user_id = $_SESSION['userid'];

$dbh = connectDB();
$cnt = count(follow($page_id));
$sql = 'UPDATE account SET follower = :cnt  WHERE user_id = :p_id';
$stmt = $dbh -> prepare($sql);
$stmt->bindValue(':cnt', $cnt);
$stmt->bindValue(':p_id', $page_id);
$stmt -> execute();

$cnt = count(follower($user_id));
$sql = 'UPDATE account SET follow = :cnt  WHERE user_id = :user_id';
$stmt = $dbh -> prepare($sql);
$stmt->bindValue(':cnt', $cnt);
$stmt->bindValue(':user_id', $user_id);
$stmt -> execute();

$url = "profile.php?userid=" . $_SESSION['page'];
header("Location: " .$url);

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
    $follow_id = $_SESSION["page"];
    $user_id = $_SESSION["userid"];
    $check = $_POST["follow"];

    if($check == 0){
        $sql = "DELETE FROM follow
	    WHERE follower = ". $user_id. " AND follow = ". $follow_id;
    } else{
        $sql = "INSERT INTO follow(follower, follow)
	    values(". $user_id. ", ". $follow_id. ")";
    }
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
}

function follow($id){
    $dbh = connectDB();
    $sql = 'SELECT * FROM follow WHERE follow = :id';
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt -> execute();

    return $stmt->fetchAll();
}

function follower($id){
    $dbh = connectDB();
    $sql = 'SELECT * FROM follow WHERE follower = :id';
    $stmt = $dbh -> prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt -> execute();

    return $stmt->fetchAll();
}
?>