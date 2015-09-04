<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();
  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
  else :
      $myUser = 0;
  endif;
  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  require_once("LookupFunctions.php");
  if (!is_null($_GET['kw'])) :
      $keyword = trim($_GET['kw']); 
      $_SESSION["kw"] = $keyword;
  endif;

  // Get the query source variables or set their default if they aren't present...
  if (!is_null($_GET['r'])) { $r = $_GET['r']; } else { $r = 1; }
  if (!is_null($_GET['a'])) { $a = $_GET['a']; } else { $a = 1; }
  if (!is_null($_GET['t'])) { $t = $_GET['t']; } else { $t = 1; }

  // The Recordings Keyword Search:
  if ($r == 1):
      $bareQuery = "SELECT DISTINCT RecordingId, Title FROM recordings WHERE (Subject  LIKE '%$keyword%' OR Description  LIKE '%$keyword%' OR Coverage  LIKE '%$keyword%' OR Keywords  LIKE '%$keyword%') ";

      // This line will restrct the query to the current instance if the $RestrictQueriesToThisInstance flag = 1,
      // AND the current user isn't logged in as the super admin (where $myUser = 1)...
      if ($RestrictQueriesToThisInstance == 1 && $myUser !== 1)  { $bareQuery .= "AND (PrefixCode = '$DefaultPrefixCode') "; }

      $queryall = $bareQuery;
      // echo "Query = $queryall <br>\n";
      $resultall = mysqli_query($conn, $queryall);
      $numberall = mysqli_num_rows($resultall);
      if ($numberall==0) { $o = "<h5 class=center>No Recordings Match<br>\"" . stripslashes($keyword) . "\".</h5>\n"; }
      $Title = "Recording Metadata<br>Containing \"" . stripslashes($keyword) . "\": ";
      $TrackId = 0;
      require("RecordingsKeywordListDisplay.php");
      $o .= "<hr>\n";
  endif;

  // The Annotations Keyword Search:
  if ($a == 1):
      $bareQuery = "SELECT DISTINCT Recordings.RecordingId, Recordings.Title, Tracks.Identifier, Tracks.TrackId, Annotation.SecondsIn FROM (Recordings INNER JOIN Tracks ON Recordings.RecordingId = Tracks.RecordingId) INNER JOIN Annotation ON Tracks.TrackId = Annotation.TrackId WHERE Annotation.Description LIKE '%$keyword%' OR Annotation.Keywords  LIKE '%$keyword%' ";

      // This line will restrct the query to the current instance if the $RestrictQueriesToThisInstance flag = 1,
      // AND the current user isn't logged in as the super admin (where $myUser = 1)...
      if ($RestrictQueriesToThisInstance == 1 && $myUser !== 1)  { $bareQuery .= "AND (Recordings.PrefixCode = '$DefaultPrefixCode') "; }

      $queryall = $bareQuery;
      $resultall = mysqli_query($conn, $queryall);
      $numberall = mysqli_num_rows($resultall);
      if ($numberall==0) { $o .= "<h5 class=center>No Annotations Match<br>\"" . stripslashes($keyword) . "\".</h5>\n"; }
      $Title = "Annotations<br>Containing \"" . stripslashes($keyword) . "\": ";

      require("RecordingsKeywordListDisplay.php");
      $o .= "<hr>\n";
  endif;


  // The Transcripts Keyword Search:
  if ($t == 1):
      $bareQuery = "SELECT DISTINCT Recordings.RecordingId, Recordings.Title, Tracks.Identifier, Tracks.TrackId FROM (Recordings INNER JOIN Tracks ON Recordings.RecordingId = Tracks.RecordingId) INNER JOIN transcripts ON Tracks.TrackId = transcripts.TrackId WHERE transcripts.Transcription LIKE '%$keyword%' ";

      // This line will restrct the query to the current instance if the $RestrictQueriesToThisInstance flag = 1,
      // AND the current user isn't logged in as the super admin (where $myUser = 1)...
      if ($RestrictQueriesToThisInstance == 1 && $myUser !== 1)  { $bareQuery .= "AND (Recordings.PrefixCode = '$DefaultPrefixCode') "; }

      $queryall = $bareQuery;
      $resultall = mysqli_query($conn, $queryall);
      $numberall = mysqli_num_rows($resultall);
      if ($numberall==0) { $o .= "<h5 class=center>No Transcriptions Match<br>\"" . stripslashes($keyword) . "\".</h5>\n"; }
      $Title = "Transcriptions<br>Containing \"" . stripslashes($keyword) . "\": ";

      require("RecordingsKeywordListDisplay.php");
  endif;
  echo $o;
?>