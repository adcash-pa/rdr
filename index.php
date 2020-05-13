<?php

session_start();

if (!empty($_GET)) {
  $_SESSION['got'] = $_GET;
  header('Location: http://localhost/rdr/');
  die;
} else {
  if (!empty($_SESSION['got'])) {
    $_GET = $_SESSION['got'];
    //unset($_SESSION['got']);

    // read csv if some GET data present
    // csv to array
    $csvData = file_get_contents('log.csv');
    $lines = preg_split("/\R/", $csvData);
    $pair = array();
    $isnew = FALSE; // new click id
    $match = FALSE; //same user sesssion with same click id
    $clickid = $_GET['action'];
    $pairmatch = array(session_id(), $clickid);
    if( isset($_SERVER['HTTPS'] ) ) {
      $current_path = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    else {
      $current_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    //compare session_id with log
    foreach ($lines as $line) {
        $pair = explode("|",$line);
        // $pair[0] - session id
        // $pair[1] - click id
        if (in_array($clickid, $pair)) {
          $isnew = FALSE;
          if ($pairmatch == $pair) {
            $match = TRUE;
            $status = "<div class='ok'>click id logged, session match (session refresh)</div>";
          }
          else {
            $status = "<div class='not-ok'>click id logged, session does not match</div>";
          }
          break;
          //echo "new click id";
        }
        else {
          $isnew = TRUE;
          $status ="<div class='ok'>new click id</div>";
        }
        $paired[] = explode("|",$line);
    }

    // write csv session log if click id new
    // abd assign session id
    if ($isnew == TRUE) {
      $line = array(session_id(). "|" . $_GET["action"]);
      $fp = fopen($_SERVER['DOCUMENT_ROOT'] .'/rdr'. "/log.csv","a");
      fputcsv($fp, $line);
      fclose($fp);
    }
  }
}

// check if any params in url and if session+click id already visited
if(count($_GET)) {

  $now = time();
  $date = substr($_GET['action'],0,10);
  $datediff = $now - $date;

  if(($now-$date) < 30 && ($isnew == TRUE || $match == TRUE)) {     // 30 min =1800 seconds
    $state = "B";
    $class = '';
    $expired = '';
  }
  else {
    $state = "W";
    $class = 'white';
    if ($match == TRUE || $isnew == TRUE) {
      $expired = '<div class="not-ok">diff expired</div>';
    }
    else {
      $expired = "<div class='not-ok'>session mismatch, don't care abour diff</div>";
    }

  }
  }
  else {
    $now = time();
    $date = substr($_GET['action'],0,10);

    if (!empty($datediff)) {
      $datediff = $now - $date;
    }

    $state = "W";
    $class = 'white';
    $expired = '<div class="not-ok">new session, no vars in url</div>';
  }

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Exhibit A</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="box <?php echo $class ?>">
    <p><?php echo $state; ?></p>
  </div>
  <div class="console">
    <?php
    if(count($_GET)) {
      echo "status:".$status."<br>";
      echo "session:".session_id()."<br>";
      echo "clickid:".$_SESSION['got']['action']."<br>";
      echo "zone:".$_SESSION['got']['zone']."<br>";
      echo "now:".$now.'<br>';
      echo "match:".$match.'<br>';
      // example: http://localhost/rdr?action=15889347301360657527057280098693807&zone=123&smt=555
      echo $current_path."?action=".$now."1360657527057280098693807&zone=123&smt=555"."<br>";
      echo $current_path;
      echo"<br><hr>";
    }
    else {
      $datediff = "no variables difined, load white hat";
    }
    ?>
    <span><?php echo 'diff:'.$datediff.'<br>'; ?></span>
    <span><?php echo $expired; ?></span>
  </div>
</body>
</html>
