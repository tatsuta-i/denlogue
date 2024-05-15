<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
require_once('connect.php'); //データベースにアクセスするphp
?>


<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
        <title>詳細</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="timeline.css"> <!-- cssとの連携 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css" rel="stylesheet">
                <script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" type="text/javascript"></script>
        <script src = "script.js"></script>
    </head>
    <body>
<!-- 右のサイドメニューの設定 -->
<div class ="prof_bar">
<div class ="prof_bar_r">
<br>

<?php
$stmt = select_pro();
$prof = $stmt->fetch();
echo "<br>";
if($prof['dot'] == 'jpg'){
    echo "<h4><span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else if($prof['dot'] == 'png'){
    echo "<h4><span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else if($prof['dot'] == 'gif'){
    echo "<h4><span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else{
    echo "<h4><span class = 'name'><img src='images/user.png' width='90' height='90' border-radius = '50%'></span>";
}
echo "<br>";
echo $prof['name']. "</h4>";
echo"<br>";
if(isset($prof["prof"])){
    echo '<h6><div class ="post_tex">'.$prof ["prof"].'<BR></div></h6>';
} else {
    echo "<h6>プロフィールが未設定です<BR></h6>";
}
echo"<br>";
echo '<form method="GET" action = "follow_page.php">';
echo "<h6><a href =\"follow_page.php?id={$prof['user_id']}\" id='clear_sample'>"; // リンクを付与
echo $prof['follow']. "フォロー</a></h6></form>";
echo '<form method="GET" action = "follower_page.php">';
echo "<h6><a href =\"follower_page.php?id={$prof['user_id']}\" id='clear_sample'>"; // リンクを付与
echo $prof['follower']. "フォロワー</a></h6></form>";
?>
</div>
</div>

<!-- 左のサイドメニューの設定 -->
<nav class="menu">
    <ul>
        <!-- タイムラインボタン -->
        <form action="timeline.php">
            <button type = "submit" name = "timeline" id = "timeline"><span class ="fa fa-bars"></span><div class ="mini-font"> タイムライン</div></button>
        </form>

        <!-- 投稿ボタン -->
        <button class="modal-open"><span class ="fa fa-plus"></span><div class ="mini-font"></div></button>

        <!-- マイページボタン -->
        <form action="mypage.php">
            <button type = "submit" name = "mypage" id = "mypage"><span class ="fa fa-home"></span><div class ="mini-font"> マイページ</div></button>
        </form>

        <!-- フォローTL -->
        <form action="follow_tl.php">
            <button type = "submit" name = "followtl" id = "followtl"><span class ="fa fa-user"><span class ="fa fa-child"></span><div class ="mini-font">フォローTL</div></button>
        </form>

        <!-- 通知 -->
        <form action="notice.php">
            <button type = "submit" name = "notice" id = "notice"><span class ="fa fa-bell"></span><div class ="mini-font">通知 <?php echo $prof['notice']; ?></div></button>
        </form>

        <!-- ログアウトボタン -->
        <form action="logout.php">
            <button type = "submit" name = "logout" id = "logout"><span class ="fa fa-angle-left"></span><div class ="mini-font"> ログアウト</div></button>
        </form>
    </ul>
</nav>
<!-- 投稿ボタンを押した時に表示される入力エリア -->
<div class="modal"></div>
<div class="modal-container">
    <div class="modal-body">
        <!-- ×ボタン -->
        <div class="modal-close">×</div>
<!-- 投稿エリア -->
<div class="modal-content">
    <form action="posta.php" method="POST" class = "modal-posta" enctype="multipart/form-data">
        <textarea id ="countUp" class="insert_post" name = "insert_post" placeholder="今どうしてる?" name="text" maxlength="140"></textarea>
        <div class = "modal-div">
            <input type = "file" name = "image" class = "modal-image" size = "30"><BR>
            <?php echo "<input type = 'hidden' name = 'parent' value = \"{$_GET["id"]}\">"; ?>
            <div class ="kaigyo"></div>
            <span id ="count1" class ="float-right">0</span><span class ="float-right">/140</span>
            <button type = "submit" name = "modal-insert" class = "modal-insert" id = "modal-insert">投稿</button>
        </div>
    </form>
</div>
<!--文字数カウント用-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $(function(){
        $('#countUp').keyup(function(){
            $('#count1').text($(this).val().length);
        });
    });
</script>
</div>
</div>
<?php
$NamePage = "詳細ページ"; // ヘッダーに表示する現在のページ名
require('header.php');
?>
    <main>
        <h1 id="top-line">
        <div style = "float:left;">
        <?php
            /*$hostname = $_SERVER['HTTP_HOST'];//ドメインを取得
            if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'],$hostname) !== false)) {
                    echo '<a href="' . $_SERVER['HTTP_REFERER'] . '"><span class ="fa fa-angle-left"></span> 戻る</a>';
            }*/
        ?>
        <button type="button" onclick="history.back()"><span class ="fa fa-angle-left"></span>戻る</button></div></h1>
        <a href="#top" id="page_top"><span class ="fa fa-angle-up"></span></a>
        <?php
        $i = 0;
        $c_id = $_GET["id"];
        while(1){
            $st = select_pare($c_id);
            $pare = $st->fetch();
            if(isset($pare['parent'])){
                $i++;
                $parent[$i] =  $pare['parent'];
                $c_id = $pare['parent'];
            } else{
                break;
            }
        }

        for($j = $i; $j > 0; $j--){
            $st_p = select_pare_post($parent[$j]);
            foreach ($st_p -> fetchAll(PDO::FETCH_ASSOC) as $pare_post){
                $id = $pare_post['user_id'];
                $st_img = select_img($id);
                $image = $st_img->fetch();

                echo"<div class =ret_post>";
                echo "<span class = 'dt'>",$pare_post['date']."</span>";
                if($pare_post['user_id'] == $_SESSION['userid']){
                    echo "<form method='GET' action = 'mypage.php'>";
                    echo "<a href =\"mypage.php\" id='clear_sample'>"; // リンクを付与
                    if($image['dot'] == 'jpg'){
                        echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else if($image['dot'] == 'png'){
                        echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else if($image['dot'] == 'gif'){
                        echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else{
                        echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    }
                    //echo "<span class = 'name' id = 'my_post'><img src='images/{$icon}' width='30' height='30'><font size='5'>",$pare_post['name']."</font><font size='2'>  ",$pare_post['log_id']."</font></span> ";
                    echo "</form>";
                    echo "<form method='POST' action='delete.php'>";
                    echo "<input type = 'hidden' name = 'delete' value = \"{$pare_post['post_num']}\">";
                    echo "<button type = 'submit' name = 'submit' id = 'submit' class ='trash'><span class ='fa fa-trash'></span></button>";
                    echo "</form>";
                } else{
                    echo "<form method='GET' action = 'profile.php'>";
                    echo "<a href =\"profile.php?userid={$pare_post['user_id']}\" id='clear_sample'>"; // リンクを付与
                    if($image['dot'] == 'jpg'){
                        echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else if($image['dot'] == 'png'){
                        echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else if($image['dot'] == 'gif'){
                        echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    } else{
                        echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$pare_post['name']."<font class = 'log-id'>  ",$pare_post['log_id']."</font></span> ";
                    }
                    //echo "<span class = 'name' ><img src='images/{$icon}' width='30' height='30'><font size='5'>",$pare_post['name']."</font><font size='2'>  ",$pare_post['log_id']."</font></span> ";
                    echo "</form>";
                }
                echo '<form method="GET" action = "syosai_page.php">';
                echo "<a href =\"syosai_page.php?id={$pare_post['post_num']}\" id='clear_sample'>"; // リンクを付与

                //echo "<p class = 'post_tex'>",$pare_post['text']."</p>"; // 投稿内容
                if($pare_post['p_dot'] == 'jpg'){
                    echo "<p class = 'post_tex'>",$pare_post['text']."</p></a></form><BR><a href = 'data:image/jpg;base64, ". base64_encode($pare_post['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/jpg;base64, ". base64_encode($pare_post['p_image']). "' width = 100' height = '100'></a>";
                } else if($pare_post['p_dot'] == 'png'){
                    echo "<p class = 'post_tex'>",$pare_post['text']."</p></a></form><BR><a href = 'data:image/png;base64, ". base64_encode($pare_post['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/png;base64, ". base64_encode($pare_post['p_image']). "' width = '100' height = '100'></a>";
                } else if($pare_post['p_dot'] == 'gif'){
                    echo "<p class = 'post_tex'>",$pare_post['text']."</p></a></form><BR><a href = 'data:image/gif;base64, ". base64_encode($pare_post['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/gif;base64, ". base64_encode($pare_post['p_image']). "' width = '100' height = '100'></a>";
                } else{
                    echo "<p class = 'post_tex'>",$pare_post['text']."</p></a></form>";
                }?>

                <div class = 'post' data-postid = '<?php echo $pare_post['post_num']; ?>'>
                    <div class= "btn-good <?php if(isGood($_SESSION['userid'], $pare_post['post_num'])) echo 'active'; ?>" >
                        <!-- 自分がいいねした投稿にはハートのスタイルを常に保持する -->
                        <i class='fa fa-heart
                            <?php if(isGood($_SESSION['userid'],$pare_post['post_num'])){ //いいね押したらハートが塗りつぶされる
                                echo ' active fas';
                            }else{ //いいねを取り消したらハートのスタイルが取り消される
                                echo ' far';
                            }; ?>
                        '></i>
                        <!-- いいね数を表示 -->
                        <span><?php echo $pare_post['good']; ?></span>
                    </div>
                    <span class ='fa fa-comment'></span> <?php echo $pare_post['comment']; ?>
                    <span class = 'postr' data-post = '<?php echo $pare_post['post_num']; ?>'>
                        <div class= "btn-retweet <?php if(isRetweet($_SESSION['userid'], $pare_post['post_num'])) echo 'act'; ?>" >
                            <!-- 自分がリツイートした投稿にはハートのスタイルを常に保持する -->
                            <i class='fa fa-repeat
                                <?php if(isRetweet($_SESSION['userid'],$pare_post['post_num'])){
                                    echo ' active fas';
                                }else{ //リツイートを取り消したらハートのスタイルが取り消される
                                    echo ' far';
                                }; ?>
                            '></i>
                            <span><?php echo $pare_post['retweet']; ?></span>
                        </div>
                    </span>
                </div></div><BR>
            <?php }
        }

        if($i > 0)
            echo "<BR>";
            
        $stmt = select();
        foreach ($stmt -> fetchAll(PDO::FETCH_ASSOC) as $message){
            $id = $message['user_id'];
            $st_img = select_img($id);
            $image = $st_img->fetch();

            echo"<div class =ret_post_main>";
            echo "<span class = 'dt'>",$message['date']."</font></span>";
            if($message['user_id'] == $_SESSION['userid']){
                echo "<form method='GET' action = 'mypage.php'>";
                echo "<a href =\"mypage.php\" id='clear_sample'>"; // リンクを付与
                if($image['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                }
                //echo "<span class = 'name' id = 'my_post'><img src='images/{$icon}' width='30' height='30'><font size='5'>",$message['name']."</font><font size='2'>  ",$message['log_id']."</font></span> ";
                echo "</form>";
                echo "<form method='POST' action='delete.php'>";
                echo "<input type = 'hidden' name = 'delete' value = \"{$message['post_num']}\">";
                echo "<button type = 'submit' name = 'submit' id = 'submit' class ='trash'><span class ='fa fa-trash'></span></button>";
                echo "</form>";
            } else{
                echo "<form method='GET' action = 'profile.php'>";
                echo "<a href =\"profile.php?userid={$message['user_id']}\" id='clear_sample'>"; // リンクを>付与
                
                if($image['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else if($image['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$message['name']."<font class = 'log-id'>  ",$message['log_id']."</font></span> ";
                }
                echo "</form>";
            }

            /* 投稿詳細phpに処理を投げる */
            echo '<form method="GET" action = "syosai_page.php">';
            echo "<a href =\"syosai_page.php?id={$message['post_num']}\" id='clear_sample'>"; // リンクを付与

            if($message['p_dot'] == 'jpg'){
                echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/jpg;base64, ". base64_encode($message['p_image']). "' width = 100' height = '100'></a>";
            } else if($message['p_dot'] == 'png'){
                echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/png;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/png;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
            } else if($message['p_dot'] == 'gif'){
                echo "<p class = 'post_tex'>",$message['text']."</p></a></form><BR><a href = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/gif;base64, ". base64_encode($message['p_image']). "' width = '100' height = '100'></a>";
            } else{
                echo "<p class = 'post_tex'>",$message['text']."</p></a></form>";
            }?>

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
            </div></div><BR>
        <?php }

        $com = select_com();
        foreach ($com -> fetchAll(PDO::FETCH_ASSOC) as $comment){
            $id = $comment['user_id'];
            $st_img = select_img($id);
            $image = $st_img->fetch();

            echo"<div class =ret_post>";
            echo "<span class = 'dt'>",$comment['date']."</font></span>";
            if($comment['user_id'] == $_SESSION['userid']){
                echo "<form method='GET' action = 'mypage.php'>";
                echo "<a href =\"mypage.php\" id='clear_sample'>"; // リンクを付与
                if($image['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else if($image['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else if($image['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                }
                echo "</form>";
                echo "<form method='POST' action='delete.php'>";
                echo "<input type = 'hidden' name = 'delete' value = \"{$comment['post_num']}\">";
                echo "<button type = 'submit' name = 'submit' id = 'submit' class ='trash'><span class ='fa fa-trash'></span></button>";
                echo "</form>";
            } else{
                echo "<form method='GET' action = 'profile.php'>";
                echo "<a href =\"profile.php?userid={$comment['user_id']}\" id='clear_sample'>"; // リンクを>付与
                if($image['dot'] == 'jpg'){
                    echo "<span class = 'name'><img src = 'data:image/jpg;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else if($image['dot'] == 'png'){
                    echo "<span class = 'name'><img src = 'data:image/png;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else if($image['dot'] == 'gif'){
                    echo "<span class = 'name'><img src = 'data:image/gif;base64, ". base64_encode($image['image']). "' width = '30' height = '30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                } else{
                    echo "<span class = 'name'><img src='images/user.png' width='30' height='30'>",$comment['name']."<font class = 'log-id'>  ",$comment['log_id']."</font></span> ";
                }
                echo "</form>";
            }
            echo '<form method="GET" action = "syosai_page.php">';
            echo "<a href =\"syosai_page.php?id={$comment['post_num']}\" id='clear_sample'>"; // リンクを付与

            if($comment['p_dot'] == 'jpg'){
                echo "<p class = 'post_tex'>",$comment['text']."</p></a></form><BR><a href = 'data:image/jpg;base64, ". base64_encode($comment['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/jpg;base64, ". base64_encode($comment['p_image']). "' width = 100' height = '100'></a>";
            } else if($comment['p_dot'] == 'png'){
                echo "<p class = 'post_tex'>",$comment['text']."</p></a></form><BR><a href = 'data:image/png;base64, ". base64_encode($comment['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/png;base64, ". base64_encode($comment['p_image']). "' width = '100' height = '100'></a>";
            } else if($comment['p_dot'] == 'gif'){
                echo "<p class = 'post_tex'>",$comment['text']."</p></a></form><BR><a href = 'data:image/gif;base64, ". base64_encode($comment['p_image']). "' data-lightbox = 'picture' data-title = '拡大'><img src = 'data:image/gif;base64, ". base64_encode($comment['p_image']). "' width = '100' height = '100'></a>";
            } else{
                echo "<p class = 'post_tex'>",$comment['text']."</p></a></form>";
            }?>

            <div class = 'post' data-postid = '<?php echo $comment['post_num']; ?>'>
                <div class= "btn-good <?php if(isGood($_SESSION['userid'], $comment['post_num'])) echo 'active'; ?>" >
                    <!-- 自分がいいねした投稿にはハートのスタイルを常に保持する -->
                    <i class='fa fa-heart
                        <?php if(isGood($_SESSION['userid'],$comment['post_num'])){ //いいね押したらハートが塗りつぶされる
                            echo ' active fas';
                        }else{ //いいねを取り消したらハートのスタイルが取り消される
                            echo ' far';
                        }; ?>
                    '></i>
                    <!-- いいね数を表示 -->
                    <span><?php echo $comment['good']; ?></span>
                </div>
                <span class ='fa fa-comment'></span> <?php echo $comment['comment']; ?>
                <span class = 'postr' data-post = '<?php echo $comment['post_num']; ?>'>
                    <div class= "btn-retweet <?php if(isRetweet($_SESSION['userid'], $comment['post_num'])) echo 'act'; ?>" >
                        <!-- 自分がリツイートした投稿にはハートのスタイルを常に保持する -->
                        <i class='fa fa-repeat
                            <?php if(isRetweet($_SESSION['userid'],$comment['post_num'])){
                                echo ' active fas';
                            }else{ //リツイートを取り消したらハートのスタイルが取り消される
                                echo ' far';
                            }; ?>
                        '></i>
                        <span><?php echo $comment['retweet']; ?></span>
                    </div>
                </span>
            </div></div><BR>
        <?php }

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

// DBから投稿内容を取得する関数
function select(){
    $dbh = connectDB();
    // DBの命令文
    $p_id = $_GET["id"];
    $sql = "SELECT *
            FROM account, post
            WHERE account.user_id=post.user_id
            AND post_num = :p_id";
    $stmt = $dbh -> prepare($sql);
    // 多分、postidを指定してあげて、whereすれば出てくる?該当のものが出てくる。つまり、postidをpostすればよい？
    $stmt -> bindValue(':p_id', $p_id);
    $stmt -> execute();
    return $stmt;
}

//親投稿があるか判定用
function select_pare($c_id){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SELECT parent
            FROM account, post
            WHERE post.user_id  = account.user_id AND post_num = :c_id";
    $stmt = $dbh -> prepare($sql);
    // 多分、postidを指定してあげて、whereすれば出てくる?該当のものが出てくる。つまり、postidをpostすればよい？
    $stmt -> bindValue(':c_id', $c_id);
    $stmt -> execute();
    return $stmt;
}

//親投稿表示用
 function select_pare_post($p_id){
    $dbh = connectDB();
    // DBの命令文
    $sql = "SELECT *
            FROM account, post
            WHERE account.user_id=post.user_id
            AND post_num = :p_id";
    $stmt = $dbh -> prepare($sql);
    // 多分、postidを指定してあげて、whereすれば出てくる?該当のものが出てくる。つまり、postidをpostすればよい？
    $stmt -> bindValue(':p_id', $p_id);
    $stmt -> execute();
    return $stmt;
}

 //子投稿表示用
function select_com(){
    $dbh = connectDB();
    // DBの命令文
    $p_id = $_GET["id"];
    $sql = "SELECT * FROM account, post WHERE account.user_id = post.user_id AND post.parent = :p_id";
    $stmt = $dbh -> prepare($sql);
    // 多分、postidを指定してあげて、whereすれば出てくる?該当のものが出てくる。つまり、postidをpostすればよい？
    $stmt -> bindValue(':p_id', $p_id);
    $stmt -> execute();
    return $stmt;
}

//アイコン表示用
function select_img($id){
    $dbh = connectDB();
    $sql = "SELECT image, dot FROM account WHERE user_id = :userid";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':userid', $id);
    $stmt->execute();

    return $stmt;
}

function select_pro(){
    $dbh = connectDB();
    $user_id = $_SESSION["userid"];
    $sql = "SELECT * FROM account WHERE ". $user_id ." = user_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    return $stmt;
}

//いいね
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
</main>
</body>
</html>
