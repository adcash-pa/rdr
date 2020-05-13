<?php

session_start();

// find current path
if( isset($_SERVER['HTTPS'] ) ) {
  $current_path = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
else {
  $current_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

if (!empty($_GET)) {
  $_SESSION['got'] = $_GET;
  header('Location: https://sales-mtmp.ew.r.appspot.com/');
  die;
} else {
  if (!empty($_SESSION['got'])) {
    $_GET = $_SESSION['got'];
    //unset($_SESSION['got']);
  }
}

// check if any params in url and if session+click id already visited
if(count($_GET)) {

  $now = time();
  $date = substr($_GET['action'],0,10);
  $datediff = $now - $date;

  if(($now-$date) < 30) {     // 30 min =1800 seconds
    header('Location: '.'https://daftpunk.com/');
  }
  else {
    header('Location: '.'https://duckduckgo.com/');
  }
  }
  else {
    $now = time();
    $date = substr($_GET['action'],0,10);

    if (!empty($datediff)) {
      $datediff = $now - $date;
    }

    header('Location: '.'https://duckduckgo.com/');
  }

?>
