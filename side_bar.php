<?php
// ログインの確認をlogin_conf.phpに投げる
require('login_conf.php');
require_once('connect.php'); //データベースにアクセスするphp

function select_pro(){
    $dbh = connectDB();
    $user_id = $_SESSION["userid"];
    $sql = "SELECT user_id, name, prof, follow, follower, image, dot, notice
            FROM account
            WHERE ". $user_id ." = user_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    return $stmt;
}
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="timeline.css">
<!-- 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<!-- 右のサイドメニューの設定 -->

<div class ="prof_bar">
<div class ="prof_bar_r">
<br>

<?php
$stmt = select_pro();
$prof = $stmt->fetch();
if($prof['dot'] == 'jpg'){
    echo "<h4><span class = 'name' id = 'my_post'><img src = 'data:image/jpg;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else if($prof['dot'] == 'png'){
    echo "<h4><span class = 'name' id = 'my_post'><img src = 'data:image/png;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else if($prof['dot'] == 'gif'){
    echo "<h4><span class = 'name' id = 'my_post'><img src = 'data:image/gif;base64, ". base64_encode($prof['image']). "' width = '90' height = '90' border-radius = '50%'></span>";
} else{
    echo "<h4><span class = 'name' id = 'my_post'><img src='images/user.png' width='90' height='90' border-radius = '50%'></span>";
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
<nav class ="menu">
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
<!-- 背景の指定 -->
<div class="modal-container">
  <div class="modal-body">
      <!-- ×ボタン -->
    <div class="modal-close">×</div>
    <!-- 投稿エリア -->
    <div class="modal-content">
        <form action="posta.php" method="POST" class = "modal-posta" enctype="multipart/form-data">
            <!--<textarea id ="countUp" class="insert_post" name = "insert_post" placeholder="今どうしてる?" name="text" maxlength="140" minlength="1"></textarea>-->
            <textarea id ="countUp" class="insert_post" name = "insert_post" placeholder="今どうしてる?" name="text" maxlength="140"></textarea>
            <div class = "modal-div">
                <input type = "file" name = "image" class = "modal-image" size = "30"><BR>
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

