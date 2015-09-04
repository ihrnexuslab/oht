<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();
  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  
  // Only proceed if there is a logged in user, otherwise, default back to the Compilations list...
  if ($uData['UserId'] > 0) :
      $myUser = $uData['UserId'];
  endif;

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("settings.php");

//  $TrackId = 1;	// For testing purposes...
  if (isset($_POST['TId'])) { $TrackId = $_POST['TId']; }
  if (isset($_POST['UId'])) { $UserId = $_POST['UId']; }
  if (isset($_POST['TrackId'])) { $TrackId = $_POST['TrackId']; }
  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }
  if (isset($_GET['UId'])) :
      $UserId = $_GET['UId'];
      $myUser = $UserId;
  endif;
  if (isset($_GET['sT'])) { $StartTime = $_GET['sT']; }

  if ($TrackId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM tracks WHERE TrackId = '$TrackId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      // $TrackId = $fields["TrackId"];
      $RecordingId = $fields["RecordingId"];
      $Identifier = $fields["Identifier"];
      $Seconds = $fields["Seconds"];
      $NumBlocks = $fields["NumBlocks"];

      $NumNewBlocks = initializeBlocks($TrackId, $myUser, $RecordingId, $NumBlocks);

      if ($NumNewBlocks == $NumBlocks) :
          // All the transcript blocks were created, go to the data entry form.
            include_once("TranscriptsEntryForm.php");
      endif;
  endif;

//-------------------------------------------------------------------------
  function initializeBlocks($TrackId, $UserId, $RecordingId, $NumBlocks) {
    global $conn;
    global $DefaultTranscriptionBlockLength;
    $Transcription = "";
    $DateAdded = date('Y-m-d');
    $NumNewBlocks = 0;
    // Loop through and create all the needed transcription blocks for the track and user.
    for ($x=0; $x<$NumBlocks; $x++) :
         $SecondsIn = $x * $DefaultTranscriptionBlockLength;
         $TranscriptId = TheNextKeyValue('TranscriptId','transcripts');
         $query="INSERT INTO transcripts (TranscriptId,RecordingId,TrackId,SecondsIn,Transcription,DateAdded,UserId) VALUES ('$TranscriptId','$RecordingId','$TrackId','$SecondsIn','$Transcription','$DateAdded','$UserId')";
         $result = mysqli_query($conn, $query);
         // Check result
         // This shows the actual query sent to MySQL, and the error. Useful for debugging.
         if (!$result) :
             $message  = 'Invalid query: ' . mysqli_error($conn);
             $message .= 'Whole query: ' . $query;
             die($message);
         else :
             $NumNewBlocks++;
         endif;
    endfor;
    return $NumNewBlocks;
  }
?>