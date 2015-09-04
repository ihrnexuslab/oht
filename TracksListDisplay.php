<?php
  $o = "";
  if ($numberall==0) :
      $o = "<h3>No Tracks Found for $myTitle!</h3>\n";
  elseif ($numberall>0) :
      $Title = "Tracks in $myTitle: ";
      $o .= "<div class=centered>\n";
      $o .= "  <table rules=rows style='width: 75%;'>\n";
      $o .= "    <tr class=tblname>\n";
      $o .= "      <td colspan=4>$Title </td>\n";
      $o .= "    </tr>\n";
      $o .= "    <tr>\n";
      $o .= "      <th class='colname' style='width: 20%'>Time</th>\n";
      $o .= "      <th class='colname'>Track</th>\n";
      $o .= "      <th class='colname' align='center' valign='middle'>Duration</th>\n";
      $o .= "      <th class='colname'>Actions</th>\n";
      $o .= "  </tr>\n";
      $x=0;
      $startTime = 0;
      while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $TrackId = $row['TrackId'];
        $FilePath = $row['FilePath'];
        $Identifier = $row['Identifier'];
        $Seconds = $row['Seconds'];
        
        // Create Output Row...
        $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
        $o .= "      <tr class=$rowclass>\n";
        $o .= "        <td class=center>" . gmdate("H:i:s", $startTime) . " - " . gmdate("H:i:s", $startTime+$Seconds) . "</td>\n";
        $o .= "        <td>$Identifier </td>\n";
        $o .= "        <td align='center' valign='middle'>" . gmdate("H:i:s", $Seconds) . "</td>\n";
        $o .= "        <td class=center>\n";
        $myAudiopath = $FIlePath . $Identifier;
        $o .= "          <a href='#' onClick='loadAudio(\"$myAudioPath\", $TrackId, 0, $startTime);'>Listen</a> &nbsp;\n";
        if ($myUser > 0) :
            // If CountTranscriptionBlocks returns > 0, then the transcription has been initialized.
            // So just open the transcription data entry form, otherwise, initialize the transcription blocks.
            // The InitTranscription.php script cretaes the empty blocks with timestamps at 5 second intervals,
            // and then includes the TranscriptionEntryForm.php script, which lets the user edit the transcription. 
            // The transcription data entry form will be opened after the transcription blocks are initialized;
            // that call is made at the bottom of the initialization routine

            $HasBlocks = (CountTranscriptionBlocks($TrackId, $myUser)>0) ? true : false;
            $o .= "          <a href='#' onClick='showTranscriptsEntry($TrackId, $myUser, \"$myAudiopath\", $HasBlocks, $startTime)'>Transcribe</a>\n";
        endif;
        $o .= "        </td>\n";
        $startTime = $startTime + $Seconds;
        $o .= "      </tr>\n";
        $x++;
      endwhile;
      // Close Table...
      $o .= "  </table>\n";
//      $o .= "  <hr>\n";
//      $o .= "  <a href='#' class=b1 onClick='processAjax(\"RecordingsLister.php?CId=$CompilationId\", \"columnLeft\");'>Recordings in this Compilation</a> &nbsp; \n";
//      $o .= "  <a href='#' class=b1 onClick='processAjax(\"CompilationsLister.php\", \"columnLeft\");'>All Compilations</a> &nbsp; \n";
      $o .= "</div>\n";
  endif;
?>