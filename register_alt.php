<link rel="stylesheet" href="timeline.css">
<?php
// セッション開始
session_start();
require_once('connect.php'); //データベースにアクセスするphp

$dbh = db_connect(); //データベースに接続
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//SQLインジェクション対策
htmlspecialchars($post_tex, ENT_QUOTES, 'UTF-8');

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//送信ボタンクリックした後の処理
if (isset($_POST['submit'])) {
   //メールアドレス空欄の場合
    $errors = array();
    if (empty($_POST['loginid'])) {
        $errors['id'] = 'IDが未入力です。';
    } else {
        $stmt = $dbh->prepare('SELECT * FROM account WHERE log_id=:id');
        $stmt->bindValue(':id', $_POST["loginid"], PDO::PARAM_STR);
        $stmt->execute();
        $row_count = $stmt->rowCount();
        if($row_count == 1){
            $errors['log_id_check'] = '既に登録されているIDです。';
        }
    }
    if (empty($_POST['username'])) {
        $errors['username'] = 'ユーザ名が未入力です。';
    }
    if (empty($_POST['password']) || empty($_POST['password2'])) {
        $errors['password'] = 'パスワードが未入力です';
    } else if ($_POST['password'] != $_POST['password2']) {
        $errors['passcheck'] = '同じパスワードを入力してください';
    }
    if (count($errors) == 0){
        // データベースに新しいユーザを挿入
        $loginid = $_POST["loginid"];
        $username = $_POST["username"];
        $password_hash = password_hash($_POST['password'],PASSWORD_DEFAULT);

        $stmt = $dbh->prepare('INSERT INTO account(log_id, name, pass) values(:id, :name, :password_hash)');
        $stmt->bindValue(':id', $loginid, PDO::PARAM_STR);
        $stmt->bindValue(':name', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = null;

        $stmt = $dbh->prepare('SELECT * FROM account WHERE log_id=:id');
        $stmt->bindValue(':id', $loginid, PDO::PARAM_STR);
        $stmt->execute();
        $member = $stmt -> fetch(PDO::FETCH_ASSOC);

        $_SESSION['userid'] = $member['user_id']; //ログインIDをセッションに保存
        $_SESSION['loginid'] = $loginid;
        $_SESSION['password'] = $_POST['password'];
        $stmt = null;
        $dbh = null;
        $message ="登録完了";
    }
}?>

<!DOCTYPE html>
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
            <?php if (isset($_POST['submit']) && count($errors) === 0){ ?>
                <!-- 登録完了画面 -->
                <p><?=$message?></p>
                <a href="login.php">ログインはこちら</a>
            <?php }else{
                /* エラーメッセージを表示 */
                foreach($errors as $value){
                    echo "<p class='error'>".$value."</p>";
                } ?>
                <p class = "login_div_p">アカウントをお持ちですか?<a href = "login.php">こちらからログイン</a></p>
                <form action="register_alt.php" method="post">
                    <label class = "login_div_label" for = "loginid">ログインID</label>
                    <input class = "login_div_input" type="text" id = "loginid" name="loginid" placeholder="ログインIDを入力" value="<?php if (!empty($_POST["loginid"])) {echo htmlspecialchars($_POST["loginid"], ENT_QUOTES);} ?>" required>
                    
                    <label class = "login_div_label" for = "username">ユーザー名</label>
                    <input class = "login_div_input" type="text" id = "username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>" required>

                    <label class = "login_div_label" for = "password">パスワード</label>
                    <input class = "login_div_input" type="password" id = "password" name="password" id = "password" placeholder="パスワードを入力" required>

                    <label class = "login_div_label" for = "password2">パスワード　確認用</label>
                    <input class = "login_div_input" type="password" id = "password2" name="password2" id = "password2" placeholder="再度パスワードを入力" required>

                    <div class = "login_div_btn_area">
                        <button class = "login_div_btn" type="submit" id ="submit" name = "submit">確認</button> <!-- class で cssの指定が可能になる -->
                    </div>
                </form>
            <?php } ?>
        </div>
    </body>
</html>