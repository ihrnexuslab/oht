<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();

  if (isset($_GET['U'])) { $UId = $_GET['U']; } else { $UId = 0;}
  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['isAdmin'] == 0 && $uData['UserId'] > 0 && $UId > 0) { $myUser = $uData['UserId']; }

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  if (isset($_GET['RId'])) { $RecordingId = $_GET['RId']; }

  $myTitle = FetchLookup("Title", "recordings", "RecordingId", $RecordingId);
  $CompilationId = FetchLookup("CompilationId", "recordings", "RecordingId", $RecordingId);

  $sorted = " order by Identifier ";

  $bareQuery = "SELECT TrackId,FilePath,Identifier,Seconds FROM tracks ";
  if (!is_null($RecordingId)) :
      $where = " Where RecordingId = '$RecordingId'";
  endif;

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  //echo "Number Found: $numberall<br>\n";

  // The next line loads a generic display loop for the table...
  require_once("TracksListDisplay.php");
  echo $o;
?>