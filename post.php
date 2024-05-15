<link rel="stylesheet" href="register.css">
<?php
//require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

require_once('connect.php'); //データベースにアクセスするphp

/*$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "hogeUser";  // ユーザー名
$db['pass'] = "hogehoge";  // ユーザー名のパスワード
$db['dbname'] = "loginManagement";  // データベース名*/

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

if (isset($_SESSION["userid"])) {
    $user_id = $_SESSION["userid"];
}

if (isset($_SESSION["logintime"])) {
    $logindate = date(DATE_RFC822, $_SESSION["logintime"]);
}

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["post_tex"])) {  // 値が空のとき
        $errorMessage = '投稿内容が未入力です。';
    }
    

    if (!empty($_POST["loginid"])) {
        // 入力したユーザIDとパスワードを格納
        $post_tex = $_POST["post_tex"];
        //$username = $_POST["username"];
        //$password = $_POST["password"];
        //$password_hash = password_hash($_POST['password'],PASSWORD_DEFAULT);
      

        // 2. ユーザIDとパスワードが入力されていたら認証する
        //$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            //$pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $dbh = db_connect(); //データベースに接続
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $dbh->prepare('INSERT INTO post(text, user_id,date) values(:post_tex, :user_id,:logindate)');
            $stmt->bindValue(':post_tex', $post_tex, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':dt', $logindate, PDO::PARAM_STR);

            //$stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT)));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）

            $stmt->execute();
            $stmt = null;
            $dbh = null;
            /*$stmt = $pdo->prepare("INSERT INTO userData(name, password) VALUES (?, ?)");

            
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる*/

            //$signUpMessage = '登録が完了しました。あなたの登録IDは '. $loginid. ' です。パスワードは'.$_POST["password"].'です';  // ログイン時に使用するIDとパスワード
            // パスワードは '. $password. ' です。
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>新規投稿画面</title>
            <link rel="stylesheet" href="register.css"> <!-- cssとの連携 -->
    </head>
    <body>
        <h1>新規投稿画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>新規投稿フォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
                <br>
                <div class = "group">
                <label class = "input-label" for="post_tex"></label>
                <label for="post_tex">つぶやく</label>
                <br>
                <input type="text" style="width:100%;height:30%;font-size:100%" id="post_tex" name="post_tex" placeholder="いまどうしてる？     (140文字まで)" value="<?php if (!empty($_POST["post_tex"])) {echo htmlspecialchars($_POST["post_tex"], ENT_QUOTES);} ?>">
                
                <i class="fas fa-eye-slash"></i>
                </div>
                <br>
                <br>
                <button class="btn-square" type="submit" id ="signUp" name = "signUp">投稿</button> <!-- class で cssの指定が可能になる -->
            </fieldset>
        </form>
        <br>
        <form action="timeline.php">
        <button>タイムラインへ戻る</button>
        </form>
    </body>
</html>
