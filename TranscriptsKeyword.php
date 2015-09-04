<?php
require_once("OralHistoryDataConn.php");
require_once("LookupFunctions.php");
require_once("settings.php");

$keyword = mysqli_real_escape_string($conn, $_POST['keyword']);

$bareQuery = "SELECT TranscriptId,RecordingId,TrackId,SecondsIn,Transcription FROM transcripts WHERE Transcription like '%$keyword%' ";
$queryall = $bareQuery;
$resultall = mysqli_query($conn, $queryall);
$numberall = mysqli_num_rows($resultall);

$Title = "All Transcripts with '%$keyword%' in the Record: ";

// The next line loads the display loop for the table...
require_once("TranscriptsListDisplay.php");

echo $o;
?>