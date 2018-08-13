<?php
  session_start();
  //セッションの破棄
  $_SESSION = [];
  //サーバー内のセッション変数のクリア
  session_destroy();
  //signin.phpへ移動
  header("Location: signin.php");
  exit();
?>


signoutや