<link rel="stylesheet" href="timeline.css">
<?php
//require 'password.php';   // password_verfy()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();


$_SESSION["userid"] = "";
require_once('connect.php'); //データベースにアクセスするphp

$dbh = db_connect(); //データベースに接続
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //例外処理を投げるようにする

// エラーメッセージの初期化
$errorMessage = "";


// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["loginid"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    } 

    // ユーザーID と パスワードが入力されていて
    if (!empty($_POST["loginid"]) && !empty($_POST["password"])) {
        // 入力したユーザIDを格納
        $loginid = $_POST["loginid"];
        try {
            // メールアドレスの形式ではなかったら
            // if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)*.dendai.ac.jp+$/", $userid)){
            //     //$errors['mail_check'] = "有効なメールアドレスではありません";
            //     $sql = "SELECT * FROM account WHERE log_id = :userid"; // DBに対する命令
            //     $stmt = $dbh->prepare($sql);
            //     $stmt -> bindvalue(':userid',$userid);
            //     $stmt -> execute();

            //     $member = $stmt -> fetch(PDO::FETCH_ASSOC);
            // } 
            // // メールアドレスの形式だったら
            // else {
            //     $sql = "SELECT * FROM account WHERE mail = :userid"; // DBに対する命令
            //     $stmt = $dbh->prepare($sql);
            //     $stmt -> bindvalue(':userid',$userid);
            //     $stmt -> execute();

            //     $member = $stmt -> fetch(PDO::FETCH_ASSOC);
            // }
                //$errors['mail_check'] = "有効なメールアドレスではありません";
            $sql = "SELECT * FROM account WHERE log_id = :loginid"; // DBに対する命令
            $stmt = $dbh->prepare($sql);
            $stmt -> bindvalue(':loginid',$loginid);
            $stmt -> execute();

            $member = $stmt -> fetch(PDO::FETCH_ASSOC);

            //パスワードとハッシュ値の対応が一致していればログイン成功
            // $memberの添字はDBのカラム
            if(password_verify($_POST['password'], $member['pass'])) {
                // ログイン情報をセッションに受け渡す処理
                $_SESSION['userid'] = $member['user_id'];
                header('Location:timeline.php');
            }else {
                echo 'ログインIDもしくはパスワードが間違っています';
            }
            
            $stmt = null; // DB切断
            $dbh = null;

        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }

        } else {
            // 4. 認証成功なら、セッションIDを新規に発行する
            // 該当データなし
            $errorMessage = 'ログインIDあるいはパスワードに誤りがあります。';
        }
    
}
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>ログイン</title>
    </head>
    <body>
        <div class = "login_div">
            <div class = "login_div_title">
                <h1>ログイン</h1>
            </div>
            <div class = "login_div_p"><p>アカウントをお持ちでない場合は<a href = "register_alt.php">新規登録</a></p></div>
            <!-- <div class = "login_div_p"><p class = "login_div_p">パスワードを忘れた場合は<a href = "pass_mail.php">こちら</a></p></div> -->
            <form  name="loginForm" method="POST">
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <br>
                <!-- <label class = "login_div_label" for="userid">ユーザーID もしくは メールアドレス</label> -->
                <label class = "login_div_label" for="loginid">ログインID</label>
                <input class = "login_div_input" type="text" id = "loginid" name="loginid" placeholder="ログインID" value="<?php if (!empty($_POST["loginid"])) {echo htmlspecialchars($_POST["loginid"], ENT_QUOTES);} ?>">

                <label class = "login_div_label" for="password">パスワード</label>
                <input class = "login_div_input"  type="password" id = "password" name="password" placeholder="パスワードを入力">
                <div class = "login_div_btn_area">
                    <button class="login_div_btn" type="submit" name = "login">ログイン</button>
                </div>
            </form>
        </div>
    </body>
</html>
