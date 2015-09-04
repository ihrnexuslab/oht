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
  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }
  if (isset($_GET['UId'])) :
      $UserId = $_GET['UId'];
      $myUser = $UserId;
  endif;
  if (isset($_GET['Seg'])) { $Segments = explode(",", $_GET['Seg']); }
  if (isset($_GET['sT'])) { $StartTime = $_GET['sT']; }

  if ($TrackId > 0) :
      // Read the Track record...
      $query="SELECT * FROM tracks WHERE TrackId = '$TrackId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $RecordingId = $fields["RecordingId"];
      $Identifier = $fields["Identifier"];
      $Seconds = $fields["Seconds"];

      $Transcription = "";
      $DateAdded = date('Y-m-d');
      $NumBlocks = 0;

      // Loop through and create all the needed transcription blocks for the track and user.
      for ($x=0; $x<count($Segments); $x=$x+3) :
           $SecondsIn = $Segments[$x+1];
           $SecondsOut = $Segments[$x+2];
           $TranscriptId = TheNextKeyValue('TranscriptId','transcripts');
           $query="INSERT INTO transcripts (TranscriptId,RecordingId,TrackId,SecondsIn,SecondsOut,Transcription,DateAdded,UserId) VALUES ('$TranscriptId','$RecordingId','$TrackId','$SecondsIn','$SecondsOut','$Transcription','$DateAdded','$UserId')";
           // echo "Segment Query = $query<br>\n";
           $result = mysqli_query($conn, $query);
           // Check result
           // This shows the actual query sent to MySQL, and the error. Useful for debugging.
           if (!$result) :
               $message  = 'Invalid query: ' . mysqli_error($conn);
               $message .= 'Whole query: ' . $query;
               die($message);
           else :
               $NumBlocks++;
           endif;

      endfor;

      if ($NumBlocks > 0) :
           $q = "UPDATE tracks SET NumBlocks = '$NumBlocks' WHERE TrackId = '$TrackId'";
           $r = mysqli_query($conn, $q);
           // Check result
           // This shows the actual query sent to MySQL, and the error. Useful for debugging.
           if (!$r) :
               $message  = 'Invalid query: ' . mysqli_error($conn);
               $message .= 'Whole query: ' . $q;
               die($message);
           endif;

          // All the transcript blocks were created, go to the data entry form.
          include_once("TranscriptsEntryForm.php");
      else :
          echo "<h3>Unable to initialize transcription blocks for $TrackId and $UserId.</h3>";
      endif;

  else :
      echo "<h3>Unable to initialize transcription blocks. No TrackId value was received.</h3>";
  endif;
?>