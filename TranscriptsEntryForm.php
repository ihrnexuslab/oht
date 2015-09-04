<SCRIPT TYPE="text/javascript">
<!-- 
//------------------------------------------------------------------------------------
  function verifyTranscript() {
    var themessage = "The following fields are required: ";
    // Put in the required fields like this:
    // if (document.form.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    if (themessage == "The following fields are required: ") {
        document.TranscriptEntryForm.action = "TranscriptsEntryForm.php"; 
        document.TranscriptEntryForm.Action.value = 'Submit'; 
        document.TranscriptEntryForm.submit();
    } else {
        alert(themessage);
        return false;
    }
  }
//  End -->
</SCRIPT>
<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  ob_start();
  session_start();
  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  $myUser = $uData['UserId'];

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $DateMod = date('Y-m-d');
  // This is how long the sound blocks are for the transcriptions.
  // It's used to calculate the endpoint for listening.
  // It only changes for the last transcription block, where it's
  // the remainder of the track length / the block length.
  $BlockLen = 5;
  $End = 5;

  //  $TrackId = 1;	// For testing purposes...
  if (isset($_POST['TId'])) { $TrackId = $_POST['TId']; }
  if (isset($_POST['UId'])) { $UserId = $_POST['UId']; }
  if (isset($_POST['TrackId'])) { $TrackId = $_POST['TrackId']; }
  if (isset($_GET['TId'])) { $TrackId = $_GET['TId']; }
  if (isset($_GET['UId'])) { $UserId = $_GET['UId']; }
  if (isset($_GET['sT'])) { $StartTime = $_GET['sT']; }

  $NumBlocks = FetchLookup("NumBlocks", "tracks", "TrackId", $TrackId);
  $RecordingId = FetchLookup("RecordingId", "tracks", "TrackId", $TrackId);
  $Seconds = FetchLookup("Seconds", "tracks", "TrackId", $TrackId);
  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :

      // Fetch the variables from the $_POST array and strip any html or php tags...
      $RecordingId = strip_tags(mysqli_real_escape_string($conn, $_POST['RecordingId']));
      $TrackId = strip_tags(mysqli_real_escape_string($conn, $_POST['TrackId']));
      $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
      $TranscriptId = $_POST['TranscriptId'];
      $Transcription = $_POST['Transcription'];
      $SecondsIn = $_POST['SecondsIn'];
      $SecondsOut = $_POST['SecondsOut'];
      for ($i=0; $i<$NumBlocks; $i++) :
           $myTranscriptId = stripslashes($TranscriptId[$i]);
           $myTranscription = stripslashes($Transcription[$i]);
           $myTranscription = addslashes($myTranscription);
           $mySecondsIn = $SecondsIn[$i];
           $mySecondsOut = $SecondsOut[$i];
           $query="UPDATE transcripts SET SecondsIn='$mySecondsIn', SecondsOut='$mySecondsOut',Transcription='$myTranscription' WHERE TranscriptId = '$myTranscriptId'";
           $result = mysqli_query($conn, $query);

           // Check result
           // This shows the actual query sent to MySQL, and the error. Useful for debugging.
           if (!$result) :
               $message  = 'Invalid query: ' . mysqli_error($conn);
               $message .= 'Whole query: ' . $query;
               die($message);
           endif;
           $Heading = "Updated Transcript. ";
      endfor;
  endif;

  $Title = "Press the 'Listen' links and transcribe what you hear into the field next to the timestamp.";
  if ($TrackId > 0 && $UserId > 0) :
      // Fetch the existing transcript records for the track...
      $query="SELECT TranscriptId, SecondsIn, SecondsOut, Transcription FROM transcripts WHERE TrackId = '$TrackId' AND UserId = '$UserId' ORDER BY TranscriptId";
      $result = mysqli_query($conn, $query);
      $numFound = mysqli_num_rows($result);
      // echo "Query = $query<br>NumFound = $numFound<br>\n";
      if ($numFound>0)  :
          $Heading .= "Edit Transcription Blocks Below...";
          $o .= "<div class=centered>\n";
          $o .= "  <h3>$Heading</h3>\n";
          $o .= "  <form name='TranscriptEntryForm' method=post>\n";
          $o .= "    <input type='hidden' name='RecordingId' id='RecordingId' value='$RecordingId'>\n";
          $o .= "    <input type='hidden' name='TrackId' id='TrackId 'value='$TrackId'>\n";
          $o .= "    <input type='hidden' name='UserId' id='UserId' value='$UserId'>\n";
          $o .= "    <input type='hidden' name='Action' id='Action' value='Submit'>\n";
          $o .= "    <input type='hidden' name='NumRegions' id='NumRegions' value='$numFound'>\n";
          $o .= "    <table class='rv' rules=groups style='width: 820px'>\n";
          $o .= "      <thead><tr class=tblname>\n";
          $o .= "        <td colspan=2>$Title</td>\n";
          $o .= "      </tr></thead>\n";

          // Loop through the array of TranscriptIds and Transcriptions, building the form as we go.
          $i=0;
          while ($i<$numFound) :
                 $fields = mysqli_fetch_assoc($result);
                 $TranscriptId[$i] = $fields["TranscriptId"];
                 $SecondsIn[$i] = $fields["SecondsIn"];
                 $SecondsOut[$i] = $fields["SecondsOut"];
                 $Transcription[$i] = stripslashes ($fields["Transcription"]);

                 if ($SecondsOut[$i] <= 0 || is_null($SecondsOut[$i])) :
                     // Calculate the end of the sound block.
                     // It's either the $SecondsIn value plus the $BlockLen,
                     // or the modulus of $Seconds (the whole track length) / $BlockLen...
                     if ($i == $numFound-1) { $End = $Seconds % $BlockLen; }
                     $EndBlock = $SecondsIn[$i] + $End;
                     $SecondsOut[$i] = $EndBlock;
                 else :
                     $EndBlock = $SecondsOut[$i];
                 endif;
                 // Continue building the form, using the transcript block
                 $o .= "      <tbody>\n";
                 $o .= "        <tr class=row0>\n";
                 $o .= "          <td>\n";
                 $o .= "            <input type='hidden' name='TranscriptId[$i]' id='TranscriptId[$i]' value=$TranscriptId[$i]>\n";
                 $o .= "            <input type='hidden' name='SecondsIn[$i]' id='SecondsIn[$i]' value=$SecondsIn[$i]>\n";
                 $o .= "            <input type='hidden' name='SecondsOut[$i]' id='SecondsOut[$i]' value=$SecondsOut[$i]>\n";
                 $o .= "            <a href='#' onClick='playBlock($SecondsIn[$i], $EndBlock);'>Listen @ " . gmdate("H:i:s", $StartTime + $SecondsIn[$i]) . "</a>\n";
                 $o .= "          </td>\n";
                 $o .= "          <td>\n";
                 $o .= "            <textarea name='Transcription[$i]' id='Transcription[$i]' wrap='SOFT' rows=3 style='width: 100%;' >" . stripslashes($Transcription[$i]) . "</textarea>\n";
                 $o .= "          </td>\n";
                 $o .= "        </tr>\n";
                 $o .= "      </tbody>\n";
                 $i++;
          endwhile;

          // Finish the form...
          $o .= "      <tfoot><tr class=footer>\n";
          $o .= "        <td colspan=2>\n";
          $o .= "          <input type='button' class='b2' id='Submit' onClick='verifyTranscript();' value='Submit'>&nbsp;&nbsp;\n";
          $o .= "          <input type='button' class='b2' id='Reset' value='Reset' onclick='document.TranscriptEntryForm.reset()'>&nbsp;&nbsp;\n";
          $o .= "          <input type='button' class='b2' id='Cancel' value='Cancel' onclick='clearDiv(\"annotations\")'>&nbsp;&nbsp;\n";
          $o .= "        </td>\n";
          $o .= "      </tr></tfoot>\n";
          $o .= "    </table>\n";
          $o .= "  </form>\n";
          $o .= "</div>\n";
          echo $o;
      endif;
  endif;
?>