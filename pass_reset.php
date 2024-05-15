<link rel="stylesheet" href="timeline.css">
<?php
// 仮登録を通らないと入れない処理
// ログインはメールアドレスもしくはログインID?

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
    if (empty($_POST["password"])) {
        $errorMessage["pass_check"] = 'パスワードが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage["pass_check"] = 'パスワードが未入力です。';
    }

    if (!empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {
        // パスワードをハッシュ化して保存


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

// パスワードを更新
if (isset($_POST["signUp"])) {
    $password_hash = password_hash($_POST['password'],PASSWORD_DEFAULT);
    try {
        $stmt = $dbh->prepare('UPDATE account SET pass = :password_hash WHERE mail = :mail');

        $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = null;

        $dbh = null;

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
        <title>パスワード変更</title>
    </head>
    <body>
        <div class = "login_div">
            <div class = "login_div_title">
                <h1>パスワード変更</h1>
            </div>
            <?php
                foreach($errorMessage as $value){
                    echo "<p class = 'error'>".$value."</p>";
                }
            ?>
            <!-- page_3 完了画面-->
            <?php if(isset($_POST['signUp']) && count($errorMessage) === 0){ ?>
                パスワードが変更されました。
                <a href="login.php">ログインページへ</a>
            <!-- page_2 確認画面-->
            <?php } elseif (isset($_POST['btn_confirm']) && count($errorMessage) === 0){ ?>
                <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
                    <p>こちらのパスワードでよろしいですか？</p>
                    <p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
                    <p>パスワード：<?=$_POST['password']?></p>
                
                    <div class = "login_div_btn_area">
                        <input type="submit" class = "login_div_btn" name="btn_back" value="戻る">
                        <input type="hidden" name="password" value="<?=htmlspecialchars($_POST["password"], ENT_QUOTES)?>">
                        <input type="hidden" name="token" value="<?=$_xPOST['token']?>">
                        <input type="submit" class = "login_div_btn" name="signUp" value="登録する">
                    </div>
                </form>

            <?php }else{ ?>
            <form action = "<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="POST">
            <p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
                <!-- エラーメッセージやサインアップのメッセージ表示 -->
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>


                <label class = "login_div_label" for="password">パスワード</label>
                <input class = "login_div_input" type="password" id = "password" name="password" placeholder="パスワードを入力" required>
                <label class = "login_div_label" for="password2">パスワード　確認用</label>
                <input class = "login_div_input" type="password"  name="password2" placeholder="再度パスワードを入力" required>

                <div class="login_div_btn_area">
                    <button class="login_div_btn" type="submit" name = "btn_confirm">確認</button>
                </div>
            </form>
            <?php } ?>
        </div>
    </body>
</html>
