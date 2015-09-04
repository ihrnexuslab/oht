<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();
  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
      $IsAdmin = $uData['IsAdmin'];
  endif;

  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  require_once("LookupFunctions.php");

//  $TrackId = 1;	// For testing purposes...
  if (isset($_POST['TId'])) { $TrackId = $_POST['TId']; }
  if (isset($_POST['TrackId'])) { $TrackId = $_POST['TrackId']; }
  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }
  if (isset($_GET['sT'])) { $StartTime = $_GET['sT']; }

  $myTrack = FetchLookup("Identifier", "tracks", "TrackId", $TrackId);
  $sorted = " ORDER BY SecondsIn ASC";
  $sortby = "SecondsIn";

  if (strlen($bareQuery)==0) :
      $bareQuery = "SELECT AnnotationId,TrackId,AnnotationTypeId,SecondsIn,SecondsOut,Description,Keywords,UserId FROM annotation ";
      $Title = "Annotations for $myTrack:";
      if (!is_null($TrackId)) :
          $where = " WHERE TrackId = '$TrackId'";
      endif;
  endif;

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  // echo "Number found = $numberall<br>\n";
  // The next line loads a generic display loop for the table...
  require_once("AnnotationListDisplay.php");
  echo $o;
?>