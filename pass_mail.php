<link rel="stylesheet" href="timeline.css">
<?php
// 実際に２回パスワードを入力して更新する場所

// セッション開始
session_start();
require_once('connect.php'); //データベースにアクセスするphp

$dbh = db_connect(); //データベースに接続
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//SQLインジェクション対策
htmlspecialchars($post_tex, ENT_QUOTES, 'UTF-8');


//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//送信ボタンクリックした後の処理
if (isset($_POST['submit'])) {
   //メールアドレス空欄の場合
    if (empty($_POST['mail'])) {
        $errors['mail'] = 'メールアドレスが未入力です。';
    }
    $mail = $_POST['mail'];
    //メールアドレス構文チェック
    // 後々、ここのifにエラーが入っていたら。。。の判定をする
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@ms.dendai.ac.jp+$/", $mail)){
        $errors['mail_check'] = "有効なメールアドレスではありません";
    }else{ // メールアドレスが既に登録されてるかどうかの確認処理
       //POSTされたデータを変数に入れる
       //DB確認  
        $sql = "SELECT * FROM account WHERE mail=:mail";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
        $stmt->execute();
        
        $row_count = $stmt -> rowCount();
       //account テーブルにメールアドレスがなかったらエラー表示
        if($row_count == 0){
            $errors['user_check'] = "このメールアドレスは登録されていません";
            // 新しいメールアドレスの
        }
        
    }

    




   //エラーがない場合、pre_accountテーブルにインサート
    if (count($errors) === 0){
        $id = hash('sha256',uniqid(rand(),1));
        $url = "http://retwitter.rd.dendai.ac.jp/test//pass_reset.php?urltoken=".$id;
       //ここでデータベースに登録する
        try{
           //例外処理を投げる（スロー）ようにする
            $sql = "INSERT INTO pre_account (urltoken, mail, date, flag) VALUES (:urltoken, :mail, now(), '0')";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':urltoken', $id, PDO::PARAM_STR);
            $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            $dbh = null;
            $message = "メールをお送りしました。24時間以内に下記のURLからパスワードを変更して下さい。";     
        }catch (PDOException $e){
            print('Error:'.$e->getMessage());
            exit();
        }
        /*
        * メール送信処理
       * 登録されたメールアドレスへメールをお送りする。
       * 今回はメール送信はしないためコメント
       */


        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        // 受信者
        $To = $mail;
        // メールタイトル
        $subject = "ReTのパスワード変更について"; 
        // メールの内容
        $body = <<< EOM
        パスワードの変更用URLをお送りしました。
        24時間以内に下記のURLからパスワードを変更して下さい。
        {$url}
        EOM;

        $Frommail = "<do_not_reply@retwitter.rd.dendai.ac.jp>";
        //Fromヘッダーを作成
        $header = 'From: '.mb_encode_mimeheader("ReT").$Frommail;

        // to, subject, message, headers
        if(mb_send_mail($To, $subject, $body, $header)){
           //セッション変数を全て解除
            $_SESSION = array();
           //クッキーの削除
           //セッションを破棄する
            session_destroy();
            $message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
        }else {
            echo "メールの送信に失敗しました。";
        }
    }
}
?>

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
    <?php if (isset($_POST['submit']) && count($errors) === 0){ ?>
        <!-- 登録完了画面 -->
        <p><?=$message?></p>
    <?php }else{ ?>
    <!-- 登録画面 -->
        <?php if(count($errors) > 0){ ?>
            <?php
            foreach($errors as $value){
                echo "<p class='error'>".$value."</p>";
            }
            ?>
        <?php } ?>
        <p class = "login_div_p">アカウントをお持ちですか?<a href = "login.php">こちらからログイン</a></p>
        <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
            <p class = "login_div_p">電大のメールアドレスを入力してください</p>
            <label class = "login_div_label" for = "mail">メールアドレス</label>
            <input class = "login_div_input" type="text" id = "mail" name="mail" placeholder="exsample: hogehoge@fuga.dendai.ac.jp"  value="<?php if( !empty($_POST['mail']) ){ echo $_POST['mail']; } ?>">
            <input type="hidden" name="token" value="<?=$token?>">
            <div class = "login_div">
                <input class="login_div_btn" type="submit" name="submit" value="送信">
            </div>
        </form>
    <?php } ?>
</div>
</body>
</html>