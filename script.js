// いいねの処理
$(function(){
  var $good = $('.btn-good'), //いいねボタンセレクタ
              goodPostId; //投稿ID
  $good.on('click',function(e){
      e.stopPropagation();
      var $this = $(this);
      //カスタム属性（postid）に格納された投稿ID取得
      // ここのデータの受け渡しができてなさそう...
      // 多分、read.php→script.js→ajaxgood.php というデータの流れ？
      goodPostId = $this.parents('.post').data('postid');
      $.ajax({
          type: 'POST',
          url: 'ajaxGood.php', //post送信を受けとるphpファイル
          data: { good_post: goodPostId} //{キー:投稿ID}
      }).done(function(data){
          console.log('Ajax Success');

          // いいねの総数を表示
          $this.children('span').html(data);
          // いいね取り消しのスタイル
          $this.children('i').toggleClass('far'); //空洞ハート
          // いいね押した時のスタイル
          $this.children('i').toggleClass('fas'); //塗りつぶしハート
          $this.children('i').toggleClass('active');
          $this.toggleClass('active');
      }).fail(function(msg) {
          console.log('Ajax Error');
      });
  });
});

// リツイートの処理
$(function(){
  var $retweet = $('.btn-retweet'), //リツイートボタンセレクタ
              PostId; //投稿ID
  $retweet.on('click',function(e){
      e.stopPropagation();
      var $this = $(this);
      PostId = $this.parents('.postr').data('post');
      $.ajax({
          type: 'POST',
          url: 'retweet.php',
          data: { retweet: PostId}
      }).done(function(data){
          console.log('Ajax Success');

          // リツイートの総数を表示
          $this.children('span').html(data);
          //リツイート取り消しのスタイル
          $this.children('i').toggleClass('far');
          // リツイート押した時のスタイル
          $this.children('i').toggleClass('fas');
          $this.children('i').toggleClass('active');
          $this.toggleClass('act');
      }).fail(function(msg) {
          console.log('Ajax Error');
      });
  });
});

// 投稿のajax通信
$(function(){
  var insert = $('.modal-insert');
  
  insert.on('click',function(){
    var $this = $(this);
    value = $this.parents('insert_section').data('value');
    $.ajax({
      type: 'POST',
      url:  'posta.php',
      data:{insert_post: value } // {キー、投稿内容}
    });
  });
});

// 投稿エリアを表示するコード
$(function(){
    // 変数に要素を入れる
    var open = $('.modal-open'),
      close = $('.modal-close'),
      container = $('.modal-container');
  
    //開くボタンをクリックしたらモーダルを表示する
    open.on('click',function(){ 
      container.addClass('active');
      // テキストエリアにカーソルがいく 失敗
      //document.posta.insert_post.focus(); 
      return false;
    });
  
    //閉じるボタンをクリックしたらモーダルを閉じる
    close.on('click',function(){  
      container.removeClass('active');
    });

    //モーダルの外側をクリックしたらモーダルを閉じる
    $(document).on('click',function(e) {
      if(!$(e.target).closest('.modal-body').length) {
        container.removeClass('active');
      }
  });
});

