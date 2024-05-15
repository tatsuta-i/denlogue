<link rel="stylesheet" href="timeline.css">
<?php
// 仮登録を通らないと入れない処理
// ログインはメールアドレスもしくはログインID?

// メールアドレスが重複していた場合、エラー文を表示させる？

// セッション開始
session_start();
require_once('connect.php'); //データベースにアクセスするphp

session_start();
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = array();
$signUpMessage = "";

$dbh = db_connect(); //データベースに接続
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ページに入って最初に行う処理
if(empty($_GET)) {
    // urlからregister.phpに飛ばなかった時の処理
	header("Location: login.php");
	exit();
}else{
	//GETデータを変数に入れる
	$urltoken = isset($_GET["urltoken"]) ? $_GET["urltoken"] : NULL;
	//メール入力判定
	if ($urltoken == ''){
		$$errorMessage['urltoken'] = "トークンがありません。";
	}else{
		try{
            // DB接続
			//flagが0の未登録者 or 仮登録日から24時間以内
			$sql = "SELECT mail FROM pre_account WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour";
            $stmt = $dbh->prepare($sql);
			$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$stmt->execute();
			
			//レコード件数取得
			$row_count = $stmt->rowCount();
			
			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count ==1){
				$mail_array = $stmt->fetch();
				$mail = $mail_array["mail"];
                $_SESSION['mail'] = $mail;
                
                // メールアドレスが既に登録されてるかどうかの確認処理 
                $sql = "SELECT * FROM account WHERE mail=:mail";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
                $stmt->execute();
                
                $row_count = $stmt -> rowCount();
                 //user テーブルに同じメールアドレスがある場合、エラー表示
                if($row_count >= 1){
                    $errors['user_check'] = "このメールアドレスはすでに利用されております。";
                    header("Location: login.php"); // 5秒後に自動的にloginページに移動
                }
            
			}else{
				$$errorMessage['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおして下さい。";
			}
			//データベース接続切断
			$stmt = null;
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			exit();
		}
	}
}
//"btn_confirm"

// ログインボタンが押された場合
if (isset($_POST["btn_confirm"])) {
    try{
    // 1. ユーザIDの入力チェック
    if (empty($_POST["loginid"])) {  // 値が空のとき
        $errorMessage['loginID_check'] = 'ログインIDが未入力です。';
    }
    else if (empty($_POST["username"])) {  // 値が空のとき
        $errorMessage['username_check'] = 'ユーザー名が未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage["pass_check"] = 'パスワードが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage["pass_check"] = 'パスワードが未入力です。';
    }

    if (!empty($_POST["loginid"]) &&!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {
        // 入力したユーザIDとパスワードを格納

        // すでに登録されているユーザーIDかを確認する
        $stmt = $dbh->prepare('SELECT * FROM account WHERE rog_id=:id');
        $stmt->bindValue(':id', $_POST["loginid"], PDO::PARAM_STR);
        $stmt->execute();
        $row_count = $stmt->rowCount();
        if($row_count == 1){
            $errorMessage['userid_check'] = '既に登録されているログインIDです。';
        }

        // 3. エラー処理
    } else if($_POST["password"] != $_POST["password2"]) {
        $errorMessage['pass_check'] = 'パスワードに誤りがあります。';
    }
}catch (PDOException $e) {
    $errorMessage['database_check'] = 'データベースエラー';
    // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
    echo $e->getMessage();
}
}

if (isset($_POST["signUp"])) {
    try {

        $loginid = $_POST["loginid"];
        $username = $_POST["username"];
        $password_hash = password_hash($_POST['password'],PASSWORD_DEFAULT);

        $stmt = $dbh->prepare('INSERT INTO account(rog_id, name, pass,mail) values(:id, :name, :password_hash,:mail)');
        $stmt->bindValue(':id', $loginid, PDO::PARAM_STR);
        $stmt->bindValue(':name', $username, PDO::PARAM_STR);
        $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = null;

        $stmt = $dbh->prepare('SELECT * FROM account WHERE rog_id=:id');
        $stmt->bindValue(':id', $loginid, PDO::PARAM_STR);
        $stmt->execute();
        $member = $stmt -> fetch(PDO::FETCH_ASSOC);

        $_SESSION['userid'] = $member['user_id']; //ログインIDをセッションに保存
        $_SESSION['loginid'] = $loginid;
        $_SESSION['password'] = $_POST['password'];
        $stmt = null;
        $dbh = null;

        //$signUpMessage = '登録が完了しました。あなたの登録IDは '. $loginid. ' です。パスワードは'.$_POST["password"].'です';  // ログイン時に使用するIDとパスワード
        // パスワードは '. $password. ' です。
    } catch (PDOException $e) {
        $errorMessage['database_check'] = 'データベースエラー';
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
}

?>




<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>新規登録</title>
    </head>
    <body>
        <div class = "login_div">
            <div class = "login_div_title">
                <h1>新規登録</h1>
            </div>
        <?php
            foreach($errorMessage as $value){
                if($value['userid_check']){
                    echo "<p class = 'error'>".$value."</p>";
                    exit;
                }
                echo "<p class = 'error'>".$value."</p>";
            }
        ?>

        <!-- page_3 完了画面-->
        <?php if(isset($_POST['signUp']) && count($errorMessage) === 0){ ?>
            本登録されました。
            <a href="timeline.php">タイムラインへ</a>
        <!-- page_2 確認画面-->
        <?php } elseif (isset($_POST['btn_confirm']) && count($errorMessage) === 0){ ?>
            <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
                <p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
                <p>ログイン用ID：<?=htmlspecialchars($_POST["loginid"], ENT_QUOTES)?></p>
                <p>氏名：<?=htmlspecialchars($_POST["username"], ENT_QUOTES)?></p>
                <p>パスワード：<?=$_POST['password']?></p>
        
                <div class = "login_div_btn_area">
                    <input class="login_div_btn" type="submit" name="btn_back" value="戻る">
                    <input type="hidden" name="token" value="<?=$_xPOST['token']?>">
                    <input type="hidden" name="loginid" value="<?=htmlspecialchars($_POST["loginid"], ENT_QUOTES)?>">
                    <input type="hidden" name="username" value="<?=htmlspecialchars($_POST["username"], ENT_QUOTES)?>">
                    <input type="hidden" name="password" value="<?=$_POST['password']?>">
                    <input class="login_div_btn" type="submit" name="signUp" value="登録する">
                </div>
            </form>

        <?php }else{ ?>
        <form action = "<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="POST">
            <p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
            <!-- エラーメッセージやログインメッセージの表示 -->
            <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
            <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
            <br>

            <label class = "login_div_label" for = "loginid">ログイン用ID</label>
            <input class = "login_div_input" type="text" id = "loginid" name="loginid" placeholder="ログインIDを入力" value="<?php if (!empty($_POST["loginid"])) {echo htmlspecialchars($_POST["loginid"], ENT_QUOTES);} ?>" required>
            
            <label class = "login_div_label" for = "username">ユーザー名</label>
            <input class = "login_div_input" type="text" id = "username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>" required>

            <label class = "login_div_label" for = "password">パスワード</label>
            <input class = "login_div_input" type="password" id = "password" name="password" id = "password" placeholder="パスワードを入力" required>

            <label class = "login_div_label" for = "password2">パスワード　確認用</label>
            <input class = "login_div_input" type="password" id = "password2" name="password2" id = "password2" placeholder="再度パスワードを入力" required>

            <div class = "login_div_btn_area">
                <button class = "login_div_btn" type="submit" id ="btn_confirm" name = "btn_confirm">確認</button> <!-- class で cssの指定が可能になる -->
            </div>
        </form>
        <?php } ?>
        </div>
    </body>
</html>
