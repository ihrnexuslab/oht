<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  // $DefaultPrefixCode is set in settings.php...
  $where = "WHERE PrefixCode = '$DefaultPrefixCode' ";

  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
      $IsAdmin = $uData['IsAdmin'];
  endif;

  // echo "IsAdmin = $IsAdmin<br>AdminsCanDelete = $AdminsCanDelete<br>";

  $UId = 0;
  if (isset($_GET['U'])) { $UId = $_GET['U']; }
  if (isset($_GET['S'])) { $mySet = $_GET['S']; }

  if ($mySet > 0) { $UId = $mySet; }
  if ($UId > 0) :
      $where .= "AND UserId = '$UId' ";
  endif;

  if (isset($_POST['CId'])) { $CompilationId = $_POST['CId']; }
  if (isset($_GET['CId'])) { $CompilationId = $_GET['CId']; }

  $o = "";
//  $o .= "  <div class=hbar></div>\n";
  $o .= "<h3 class=center>Compilations in the Oral History Project</h3><p>In the data structure we have developed for the project, \"Compilations\" refer to groups of audio resources related to the same theme or subject. These typically include interviews about the theme or subject of the Compilation, and we refer to these interviews as \"Recordings.\" Because recordings are often quite lengthy -- sometimes more than an hour -- we have divided them into \"Tracks,\" which are usually no more than three minutes in length. We do this to prevent issues in streaming very long .mp3 files. You'll see the available tracks when you click the \"List Recordings\" link next to a Compilation in the list shown below.</p>\n";

  $sorted = " ORDER BY Description ";
  $sortby = "Description";

  if (strlen($bareQuery)==0) :
      $bareQuery = "SELECT CompilationId,Description FROM compilations ";
      $Title = "List of Compilations in the ASU Social Scribe Project: ";
      if (!is_null($CompilationId)) :
          $where .= "AND CompilationId = '$CompilationId'";
      endif;
  endif;

  $queryall = $bareQuery.$where.$sorted.$querylimit;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  //echo "Number Found: $numberall<br>\n";

  // The next line loads a generic display loop for the table...
  require_once("CompilationsListDisplay.php");
  echo $o;
?>