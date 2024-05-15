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

            if(isset($_POST['prof_post'])){
                if($_POST['prof_post'] == NULL){
                    $stmt = $dbh->prepare('UPDATE account SET prof = NULL WHERE user_id = :user_id');
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                } else{
                    //SQLインジェクション対策
                    htmlspecialchars($prof_tex, ENT_QUOTES, 'UTF-8');

                    // DBの命令文
                    $prof_tex = $_POST['prof_post'];
                    $stmt = $dbh->prepare('UPDATE account SET prof = :prof_tex WHERE user_id = :user_id');
                    $stmt->bindValue(':prof_tex', htmlspecialchars($prof_tex), PDO::PARAM_STR);
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                }
                $stmt->execute();
            } else if(isset($_POST['name_post'])){
                if($_POST['name_post'] != NULL){
                    //SQLインジェクション対策
                    htmlspecialchars($prof_tex, ENT_QUOTES, 'UTF-8');

                    $name = $_POST['name_post'];
                    $stmt = $dbh->prepare('UPDATE account SET name = :name WHERE user_id = :user_id');
                    $stmt->bindValue('name', htmlspecialchars($name), PDO::PARAM_STR);
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
            $stmt = null;
            $dbh = null;
    
            // 処理が終わったら自動的にtimeline.phpに戻る
            if($dbh == null){
                header('Location: mypage.php');
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }

?>