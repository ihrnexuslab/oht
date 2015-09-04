<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  if (isset($_GET['U'])) { $UId = $_GET['U']; } else { $UId = 0;}
  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
      $IsAdmin = $uData['IsAdmin'];
  endif;

  if ($uData['isAdmin'] == 0 && $uData['UserId'] > 0 && $UId > 0) :
      $where = "WHERE UserId = '$myUser' ";
  endif;

  if (isset($_POST['CId'])) { $CompilationId = $_POST['CId']; }
  if (isset($_GET['CId'])) { $CompilationId = $_GET['CId']; }
  if ($DefaultCompilationId > 0) {$CompilationId = $DefaultCompilationId;}

  if (isset($_POST['RId'])) { $RecordingId = $_POST['RId']; }
  if (isset($_GET['RId'])) { $RecordingId = $_GET['RId']; }

  if ($myUser > 0 && $UseCompilationsUsers == 1) {$permissions = FetchPermissions($CompilationId, $myUser); }

  $myTitle = FetchLookup("Description", "compilations", "CompilationId", $CompilationId);

  $sorted = " ORDER BY Title ";
  $sortby = "Title";

  $bareQuery = "SELECT RecordingId,Title,UserId,Publish FROM recordings ";
  if (!is_null($CompilationId)) :
      $where = " Where CompilationId = '$CompilationId'";
  endif;

  if (!is_null($RecordingId)) :
      $where = " Where RecordingId = '$RecordingId'";
  endif;

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>myUser=$myUser\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  // echo "Number Found: $numberall<br>\n";

  // The next line loads a generic display loop for the table...
  require_once("RecordingsListDisplay.php");
  echo $o;
?>