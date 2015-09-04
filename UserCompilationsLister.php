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
      $UserName = $uData['UserName'];
  endif;

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $UId = 0;
  if (isset($_GET['U'])) { $myUser = $_GET['U']; }

  $Title = "Compilations where $UserName is a member:";

  $o = "";
//  $o .= "  <div class=hbar></div>\n";
  $o .= "<h3 class=center>Compilations in the Oral History Project</h3><p>In the data structure we have developed for the project, \"Compilations\" refer to groups of audio resources related to the same theme or subject. These typically include interviews about the theme or subject of the Compilation, and we refer to these interviews as \"Recordings.\" Because recordings are often quite lengthy -- sometimes more than an hour -- we have divided them into \"Tracks,\" which are usually no more than three minutes in length. We do this to prevent issues in streaming very long .mp3 files. You'll see the available tracks when you click the \"List Recordings\" link next to a Compilation in the list shown below.</p>\n";

  $sorted = " ORDER BY compilations.Description";

  $bareQuery = "SELECT compilations.Description, compilation2user.CompilationId, compilation2user.UserId, compilation2user.IsAdmin, compilation2user.CanUpload, compilation2user.CanAnnotate, compilation2user.CanDownload, compilation2user.CanAdd, compilation2user.CanModify FROM (compilations INNER JOIN compilation2user ON compilations.CompilationId = compilation2user.CompilationId) INNER JOIN users ON compilation2user.UserId = users.UserId ";
  $where = "WHERE users.UserId = '$myUser' ";

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  //echo "Number Found: $numberall<br>\n";

  // The next line loads a generic display loop for the table...
  require_once("UserCompilationsListDisplay.php");
  echo $o;
?>