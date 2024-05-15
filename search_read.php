<!-- ここからPHPの処理 -->
<link rel="stylesheet" href="timeline.css">
<?php
require_once('connect.php'); //データベースにアクセスするphp

$stmt = select();
// select関数で取得したDBデータを $message に格納
foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $message){
    // 取得したDBデータから何を表示するかを設定する
    // 添字はDBのカラムで指定

    $id = $message['user_id'];
    /*$ico = select_icon($id);
    if($ico == NULL){
        $icon = 'user.png';
    } else{
        $icon = $id.'.'.$ico;
    }*/
    $st_img = select_img($id);
    $image = $st_img->fetch();
    ?>
    <div class =ret_post_main>
        <!-- リツイートの場合、元のツイートを表示 -->
        <?php if($message['ret_num'] != NULL){
            echo $message['name']. "さんがリツイートしました<BR>";?>
            <?php if($message['user_id'] == $_SESSION['userid']){ ?>
            <form method='POST' action='retweet.php'>
            <input type = 'hidden' name = 'retweet' value = "<?php echo $message['ret_num']; ?>">
            <input type = 'hidden' name = 'del_ret' value = "1">
            <button type = 'submit' name = 'submit' id = 'submit' class = 'trash'><span class ='fa fa-trash'></span></button>
            </form>
            <?php }
            select_retweet($message['ret_num']);
        } else{ ?>
            <!-- 投稿日時を表示 -->
            <span class = 'dt'><?php echo $message['date']; ?></span>

            <?php if($message['user_id'] == $_SESSION['userid']){ ?>
                <!-- アイコン, ユーザー名, ログインIDの表示-->
                <form method='GET' action = 'mypage.php'>
                    <a href ="mypage.php" id='clear_sample' >
                    <!-- 画像の拡張子で判別 -->
                    <?php if($image['dot'] == 'jpg'){ ?>
                    <?php
                        /* <span class = 'name' id = 'my_post'><img src = 'data:image/jpg;base64, <?php echo base64_encode($image['image']); ?>' width = '30' height = '30'>,<?php $message['name'] ?><font class = 'log-id'>  ,<?php $message['log_id'] ?></font></span> */
                        echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                    } else if($image['dot'] == 'png'){
                        echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                    } else if($image['dot'] == 'gif'){
                        echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                    } else{
                        echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                    }
                    //echo "<span class = 'name' ><img src='images/{$icon}' width='30' height='30'>",$message['name']."</font><font class ='log-id'> ",$message['log_id']."</font></span> ";
                    ?>
                </form>
                <!-- 削除ボタン -->
                <form method='POST' action='delete.php'>
                    <input type = 'hidden' name = 'delete' value = "<?php echo $message['post_num']; ?>">
                    <button type = 'submit' name = 'submit' id = 'submit' class = "trash"><span class ='fa fa-trash'></span></button>
                </form>
            <?php } else{ ?>
                <form method='GET' action = 'profile.php'>
                <a href ="profile.php?userid= <?php echo $message['user_id']; ?>" id='clear_sample'>
                <?php if($image['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                }
                //echo "<span class = 'name' ><img src='images/{$icon}' width='30' height='30'>",$message['name']."</font><font class ='log-id'> ",$message['log_id']."</font></span> ";
                echo "</form>";

            } ?>
            <!-- 投稿内容や画像を表示する処理 -->
            <form method="GET" action = "syosai_page.php">
                <!-- <input type = hidden name = 'read' value = \"{$message['post_num']}
                    <a href =\"javascript:readform.submit()\" id='clear_sample'> -->
                <a href ="syosai_page.php?id= <?php echo $message['post_num']; ?>" id='clear_sample'>

                <!-- //echo "<span class = 'dt'>",$message['date']."</font></span>";
                //echo "<p class = 'post_tex'>",$message['text']."</p>"; // 投稿内容 -->
                <?php if($message['p_dot'] == 'jpg'){
                    echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '", $message['name']. $message['post_num']. "'><img src = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' width = 100' height = '100'></a>";
                } else if($message['p_dot'] == 'png'){
                    echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/png;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '", $message['name']. $message['post_num']. "'><img src = 'data:image/png;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
                } else if($message['p_dot'] == 'gif'){
                    echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '", $message['name']. $message['post_num']. "'><img src = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
                } else{
                    echo "<p class = 'post_tex'>",$message['text']."</p></a></form>";
                } ?>

            <!-- <form method = 'POST' action = 'posta.php'> -->
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
        <?php } ?>
    </div>
    <?php echo"<br>";
}
$SearchWord;

// DBから投稿内容を取得する関数
function select(){
    $dbh = connectDB();
    global $SearchWord;
    // DBの命令文
    //$SearchWord = $_POST["search"]; // 検索されたワードを$SearchWordに代入
    //クエリ文の前半作成
    $front_query =' SELECT *
                    FROM account, post WHERE (post.user_id = account.user_id AND post.parent IS NULL) AND ';
    // クエリ文の後半作成
    $back_query = ' ORDER BY date DESC
                    LIMIT 100 ';
    if($SearchWord){ // 検索ワードがある場合
        $count = 1; //カウントを1にする
        $SearchWord = trim($SearchWord); // 検索されたワードの前後のスペースのみ削除
        $SearchWord = str_replace("　"," ",$SearchWord); //全角スペースがあれば、半角スペースに変換


        /* 複数のワードで検索された場合の処理 */
        if(stristr($SearchWord," ")){
            $S_Word = explode(" ",$SearchWord); // 半角スペースがあれば、検索文字列を分ける
            $count  = count($S_Word); // 配列の数をカウント

            for($i = 0; $i < $count;$i++){
                if($i != "0"){
                    $OR = $OR." OR ";
                } else{
                    $OR = "(";
                }
                $OR = $OR." text LIKE :SearchWord".$i;
            }
            $OR = $OR." )";
        }
    }else { // 何も検索されていない場合
        $count = 0;
    }

    /* 検索ワードが空白なら検索しない */
    if($SearchWord <> ""){
        // 検索ワードが一つの場合
        if($count == 1){
            $OR = ' text LIKE :SearchWord ';
            $sql = $front_query.$OR.$back_query;
            $stmt = $dbh -> prepare($sql);
            $stmt -> bindValue(':SearchWord', '%'.$SearchWord.'%');
            $stmt -> execute();
            return $stmt;
        }

        // 検索ワードが複数の処理
        else if($count > 1){
            $sql = $front_query.$OR.$back_query;
            $stmt = $dbh -> prepare($sql);
            for($i = 0; $i < $count; $i++){
                $S_Word2[$i] = '%'.$S_Word[$i].'%';
                $holder = ':SearchWord'.$i;
                $stmt -> bindValue($holder,$S_Word2[$i]);
            }
            $stmt -> execute();
            return $stmt;
        }
    }
    // 検索ワードが空白なら全ての投稿を表示する
    //echo "何も単語を入れないで検索したら全ての投稿を表示する方が良いのかな？";
    $OR = ' text LIKE :SearchWord ';
    $sql = $front_query.$OR.$back_query;
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':SearchWord', '%');
    $stmt -> execute();
    return $stmt;
}

function select_img($id){
    $dbh = connectDB();
    $sql = "SELECT image, dot FROM account WHERE user_id = :userid";
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
?>