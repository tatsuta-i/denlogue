<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
require_once('connect.php'); //データベースにアクセスするphp
?>

<!-- ここからPHPの処理 -->
<link rel="stylesheet" href="timeline.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" type="text/javascript"></script>

<?php
$user_id = $_SESSION['userid'];
$stmt = select_notice($user_id);
$st = select_img($user_id);
$image = $st->fetch();
$i = 0;

// select関数で取得したDBデータを $message に格納
foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $notice){
    $stmts = select($notice['post_num']);
    $message = $stmts->fetch();
    $name = name($notice['send_id']);
    ?>
    <!-- 投稿日時を表示 -->
    <?php
        if($i < $image['notice']){
            echo "<div class = ret_post_main>";
        } else {
            echo "<div class = ret_post>";
        }
    ?>
        <form method="GET" action = "profile.php">
            <span><a href ="profile.php?userid= <?php echo $notice['send_id']; ?>" id='clear_sample'>
            <?php
                if($notice['flag'] == 0){
                    echo $name. "さん</a>がこの投稿にいいねしました</span>";
                } else if($notice['flag'] == 1){
                    echo $name. "さん</a>がこの投稿を拡散しました</span>";
                } else{
                    echo $name. "さん</a>がこの投稿にコメントしました</span>";
                }
            ?>
        </form>

        <span class = 'dt'><?php echo $message['date']; ?></span>
    <!-- アイコン, ユーザー名, ログインIDの表示-->
    <form method='GET' action = 'mypage.php'>
        <a href ="mypage.php" id='clear_sample' >
        <!-- 画像の拡張子で判別 -->
        <?php if($image['dot'] == 'jpg'){
            echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['rog_id']."</font></span> ";
        } else if($image['dot'] == 'png'){
            echo "<span class = 'name' id = 'my_post'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['rog_id']."</font></span> ";
        } else if($image['dot'] == 'gif'){
            echo "<span class = 'name' id = 'my_post'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['rog_id']."</font></span> ";
        } else{
            echo "<span class = 'name' id = 'my_post'><img src='images/user.png' width='30' height='30'>",$message['name']."<font class = 'log-id'>  ",$message['rog_id']."</font></span> ";
        }
        ?>
    </form>
    <!-- 削除ボタン -->
    <form method='POST' action='delete.php'>
        <input type = 'hidden' name = 'delete' value = "<?php echo $message['post_num']; ?>">
        <button type = 'submit' name = 'submit' id = 'submit' class = "trash"><span class ='fa fa-trash'></span></button>
    </form>

    <!-- 投稿内容や画像を表示する処理 -->
    <form method="GET" action = "syosai_page.php">
        <a href ="syosai_page.php?id= <?php echo $message['post_num']; ?>" id='clear_sample'>
        <?php if($message['p_dot'] == 'jpg'){
            echo "<p class = 'post_tex'>",$message['post_tex']."</p></a></form><BR><a href = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' width = 100' height = '100'></a>";
        } else if($message['p_dot'] == 'png'){
            echo "<p class = 'post_tex'>",$message['post_tex']."</p></a></form><BR><a href = 'data:image/png;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/png;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
        } else if($message['p_dot'] == 'gif'){
            echo "<p class = 'post_tex'>",$message['post_tex']."</p></a></form><BR><a href = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
        } else{
            echo "<p class = 'post_tex'>",$message['post_tex']."</p></a></form>";
        } ?>
    <div class = 'post' data-postid = '<?php echo $message['post_num']; ?>'>
        <div class= "btn-good <?php if(isGood($_SESSION['userid'], $message['post_num'])) echo 'active'; ?>" >
            <!-- 自分がいいねした投稿にはハートのスタイルを常に保持する -->
            <i class='fa fa-heart
                <?php if(isGood($_SESSION['userid'],$message['post_num'])){ //いいね押したらハートが塗りつぶされる
                    echo ' active fas';
                }else{ //いいねを取り消したらハートのスタイルが取り消される
                    echo ' far';
                }; ?>
            '></i>
            <!-- いいね数を表示 -->
            <span><?php echo $message['good']; ?></span>
        </div>
        <span class ='fa fa-comment'></span> <?php echo $message['comment']; ?>
        <span class = 'postr' data-post = '<?php echo $message['post_num']; ?>'>
            <div class= "btn-retweet <?php if(isRetweet($_SESSION['userid'], $message['post_num'])) echo 'act'; ?>" >
                <!-- 自分がリツイートした投稿にはハートのスタイルを常に保持する -->
                <i class='fa fa-repeat
                    <?php if(isRetweet($_SESSION['userid'],$message['post_num'])){
                        echo ' active fas';
                    }else{ //リツイートを取り消したらハートのスタイルが取り消される
                        echo ' far';
                    }; ?>
                '></i>
                <span><?php echo $message['retweet']; ?></span>
            </div>
        </span>
    </div>
</div><BR>
<?php
$i++;
}
update($user_id);
//$p_numの番号の投稿を取得
function select($p_num){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SELECT *
            FROM account, post
            WHERE account.user_id=post.user_id AND post.post_num = :p_num";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':p_num', $p_num);
    $stmt -> execute();
    return $stmt;
}

//通知一覧取得
function select_notice($id){
    $dbh = connectDB();
    $sql = "SELECT notice.post_num, notice.send_id, notice.flag
            FROM account, notice
            WHERE account.user_id = notice.user_id AND notice.user_id = :user_id
            ORDER BY id desc";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':user_id', $id);
    $stmt -> execute();
    return $stmt;
}

//名前の取得
function name($id){
    $dbh = connectDB();
    $sql = "SELECT name
            FROM account
            WHERE user_id = :user_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':user_id', $id);
    $stmt -> execute();
    $name = $stmt->fetch();
    return $name['name'];
}
//アイコンと通知数取得
function select_img($id){
    $dbh = connectDB();
    $sql = "SELECT image, dot, notice FROM account WHERE user_id = :userid";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':userid', $id);
    $stmt->execute();

    return $stmt;
}

//いいね機能
function isGood($u_id, $p_id){
    try {
        $dbh = db_Connect();
        $sql = 'SELECT * FROM Good WHERE post_num = :p_id AND user_id = :u_id';
        // クエリ実行
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $u_id);
        $stmt -> execute();

        if($stmt->rowCount()){
            // いいね
            return true;
        }else{
            // いいねしてない
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}
//リツイート機能
function isRetweet($u_id, $p_id){
    try {
        $dbh = db_Connect();
        $sql = 'SELECT * FROM post WHERE ret_num = :p_id AND user_id = :u_id';
        // クエリ実行
        $stmt = $dbh -> prepare($sql);
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $u_id);
        $stmt -> execute();

        if($stmt->rowCount()){
            // リツイート
            return true;
        }else{
            // リツイートしてない
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//通知数を0に
function update($id){
    $dbh = connectDB();
    $sql = "UPDATE account
            SET notice = 0
            WHERE user_id = :user_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':user_id', $id);
    $stmt -> execute();
    $name = $stmt->fetch();
}?>
