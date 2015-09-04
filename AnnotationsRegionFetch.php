<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }

  $sorted = " ORDER BY SecondsIn ASC";
  $sortby = "SecondsIn";

  if (strlen($bareQuery)==0) :
      $bareQuery = "SELECT SecondsIn FROM annotation ";
      if (!is_null($TrackId)) :
          $where = " WHERE TrackId = '$TrackId'";
      endif;
  endif;

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  //echo "Number found = $numberall<br>\n";
  $x = 0;
  while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $SecondsIn=$row['SecondsIn'];
        $o .= "$x,$SecondsIn;";
        $x++;
  endwhile;
  $o = rtrim($o, ";");
  echo $o;
?>