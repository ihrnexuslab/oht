<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();

  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }
  if (isset($_GET['U'])) { $UId = $_GET['U']; } else { $UId = 0;}
  if (isset($_SESSION['s_uData'])) 
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
      $IsAdmin = $uData['IsAdmin'];
  endif;
  $where = "WHERE TrackId = '$TrackId' ";

  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  require_once("LookupFunctions.php");

  $Identifier = FetchLookup("Identifier", "tracks", "TrackId", $TrackId);
  $RecordingId = FetchLookup("RecordingId", "tracks", "TrackId", $TrackId);
  $Seconds = FetchLookup("Seconds", "tracks", "TrackId", $TrackId);
  $NumBlocks = FetchLookup("NumBlocks", "tracks", "TrackId", $TrackId);
  $CId = FetchLookup("CompilationId", "recordings", "RecordingId", $RecordingId);

  $LastBlockLength = $DefaultTranscriptionBlockLength;
  if (($Seconds % $DefaultTranscriptionBlockLength) > 0) { $LastBlockLength = ($Seconds % $DefaultTranscriptionBlockLength); }

  $Title = "Transcript of $Identifier:";

  $sorted = "ORDER BY TranscriptId, UserId";
  $bareQuery = "SELECT TranscriptId,RecordingId,TrackId,SecondsIn,Transcription,UserId FROM transcripts ";
  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);

  // The next line loads a generic display loop for the table...
  require_once("TranscriptsListDisplay.php");

  echo $o;
?>