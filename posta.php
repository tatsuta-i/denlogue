<link rel="stylesheet" href="register.css">
<?php
//require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

require_once('connect.php'); //データベースにアクセスするphp

// 入力フォームから情報を受け取る
$post_tex = $_POST['insert_post'];
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

            //SQLインジェクション対策
            htmlspecialchars($post_tex, ENT_QUOTES, 'UTF-8');

            // DBの命令文
            $file = $_FILES["image"]["tmp_name"];
            if($file==""){
                if($post_tex != NULL){
                    $stmt = $dbh->prepare('INSERT INTO post(text, user_id, date) values(:post_tex, :user_id, now())');
                    $stmt->bindValue(':post_tex', htmlspecialchars($post_tex), PDO::PARAM_STR);
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                    $stmt->execute();
                }
            } else{
                $imgdat = file_get_contents($file);
                $dot = substr(strchr($_FILES['image']['name'], '.'), 1);

                $sql = "INSERT INTO post(text, user_id, date, image, dot) values(:post_tex, :user_id, now(), :imgdat, :dot)";
                $stmt = $dbh -> prepare($sql);
                $stmt->bindValue(':post_tex', htmlspecialchars($post_tex), PDO::PARAM_STR);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                $stmt->bindValue(':imgdat', $imgdat);
                $stmt->bindValue(':dot', $dot);
                $stmt->execute();
            }

            if(isset($_POST['parent'])){
                $st = select_com_post();
                foreach ($st -> fetchAll(PDO::FETCH_ASSOC) as $message){
                    $chil = $message['post_num'];
                }
                // $st_c = select_come($chil);
                noti_com($_POST['parent']);

                // いいね数を表示 テストコード
                $p_id = $_POST['parent'];
                pare();
                $cnt = count(get_com($p_id));
                $sql = 'UPDATE post SET comment = :cnt WHERE post_num = :p_id';
                $st_com = $dbh -> prepare($sql);
                $st_com->bindValue(':cnt', $cnt);
                $st_com->bindValue(':p_id', $p_id);
                $st_com -> execute();

                $url = "syosai_page.php?id=" . $_POST['parent'];
                header("Location: " .$url);
            } else{
                header('Location: timeline.php');
            }    
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
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

        function noti_com($p_id){
            $user_id = $_SESSION['userid'];
            $dbh = connectDB();
            $stmt = $dbh->prepare('SELECT user_id from post where post_num = :p_id');
            $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
            $stmt->execute();
            $to_id = $stmt->fetch();
            $stmt = $dbh->prepare('UPDATE account set notice = notice + 1 where user_id = :user_id;');
            $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt = $dbh->prepare('INSERT INTO notice(user_id, send_id, flag, post_num) values(:user_id, :s_id, 2, :p_num)');
            $stmt->bindValue(':user_id', $to_id['user_id'], PDO::PARAM_STR);
            $stmt->bindValue(':s_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':p_num', $p_id, PDO::PARAM_STR);
            $stmt->execute();
        }

        function select_com_post(){
            $dbh = connectDB();
            // DBの命令文
            $user_id = $_SESSION["userid"];
            $sql = "SELECT post_num
                    FROM account, post
                    WHERE account.user_id=post.user_id AND :user_id = post.user_id
                    ORDER BY date DESC
                    LIMIT 1";
            $stmt = $dbh -> prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $stmt -> execute();
            return $stmt;
        }

        function select_come($chil){
            $dbh = connectDB();
            // DBの命令文
            $pare = $_POST['parent'];
            $stmts = $dbh->prepare('INSERT INTO comment values(:parent, :child)');
            $stmts->bindValue(':parent', $pare, PDO::PARAM_STR);
            $stmts->bindValue(':child', $chil, PDO::PARAM_STR);
            $stmts->execute();
            return $stmt;
        }

        function pare(){
            $dbh = connectDB();

            $user_id = $_SESSION['userid'];
            $stmt = $dbh->prepare('UPDATE post SET parent = :parent WHERE user_id = :userid ORDER BY date DESC LIMIT 1');
            $stmt->bindValue(':parent', $_POST['parent'], PDO::PARAM_STR);
            $stmt->bindValue(':userid', $user_id);
            $stmt->execute();
        }
        // コメント数を獲得する
        function get_com($p_id){
            try {
                $dbh = db_Connect();
                $sql = 'SELECT * FROM post WHERE parent = :p_id';
                $stmt = $dbh -> prepare($sql);
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