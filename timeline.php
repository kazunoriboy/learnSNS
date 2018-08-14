<?php
  session_start();
  require('dbconnect.php');
  //ログインユーザー情報の取得
  $sql = 'SELECT * FROM `users` WHERE `id`=?';
  $data = array($_SESSION["id"]);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  $login_user = $stmt->fetch(PDO::FETCH_ASSOC);

  $errors = array();

  if(!empty($_POST)) {
    $feed = $_POST['feed'];

    if($feed == ''){
      $errors['feed'] = 'blank';
    } else {
      $sql = 'INSERT INTO `feeds` SET `feed`=?, `user_id`=?, `created`=NOW()';
      $data = array($feed, $_SESSION['id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

      header('Location: timeline.php');
      exit();
    }
  }//POST送信されたら終わり

  //一覧データの取得
  $feeds = array();
  $sql = 'SELECT `feeds`.*,`users`.`name`,`users`.`img_name` FROM `feeds` JOIN `users` ON `feeds`.`user_id`=`users`.`id` ORDER BY `created` DESC';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  while(1) {
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if($rec == false){
      break;
    }
    $feeds[] = $rec;
  }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body style="margin-top: 60px; background: #E4E6EB;">
  <?php include('nav.php'); ?>
  <div class="container">
    <div class="row">
      <div class="col-xs-3">
        <ul class="nav nav-pills nav-stacked">
          <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
          <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <!-- <li><a href="timeline.php?feed_select=follows">フォロー</a></li> -->
        </ul>
      </div>
      <div class="col-xs-9">
        <div class="feed_form thumbnail">
          <form method="POST" action="">
            <div class="form-group">
              <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
            </div>
            <input type="submit" value="投稿する" class="btn btn-primary">
          </form>
          <?php if(isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
            <p class="alert alert-danger">投稿データを入力してください</p>
          <?php } ?>
        </div>
        <?php foreach($feeds as $key => $feed) { ?>
          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="<?php echo 'user_profile_img/'.$feed['img_name']; ?>" width="40">
              </div>
              <div class="col-xs-11">
                <?php echo $feed['name']; ?><br>
                <a href="#" style="color: #7F7F7F;"><?php echo $feed['created']; ?></a>
              </div>
            </div>
            <div class="row feed_content">
              <div class="col-xs-12" >
                <span style="font-size: 24px;"><?php echo $feed['feed']; ?></span>
              </div>
            </div>
            <div class="row feed_sub">
              <div class="col-xs-12">
                <form method="POST" action="" style="display: inline;">
                  <input type="hidden" name="feed_id" >
                    <input type="hidden" name="like" value="like">
                    <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！</button>
                </form>
                <span class="like_count">いいね数 : 100</span>
                <span class="comment_count">コメント数 : 9</span>
                  <a href="#" class="btn btn-success btn-xs">編集</a>
                  <a href="#" class="btn btn-danger btn-xs">削除</a>
              </div>
            </div>
          </div>
        <?php } ?>
        <div aria-label="Page navigation">
          <ul class="pager">
            <li class="previous disabled"><a href="#"><span aria-hidden="true">&larr;</span> Newer</a></li>
            <li class="next"><a href="#">Older <span aria-hidden="true">&rarr;</span></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>
