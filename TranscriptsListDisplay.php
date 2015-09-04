<?php
if ($numberall==0) :
    $o = "<h3>No Transcript Records Found!</h3>\n";
elseif ($numberall>0) :
    $o .= "<div class=centered>\n";
    $o .= "  <table class='rv' rules=none style='width:100%; font-size: 8.5pt'>\n";
    $o .= "    <tr class=tblname>\n";
    $o .= "      <td colspan=2>$Title </td>\n";
    $o .= "    </tr>\n";
    $o .= "    <tr>\n";
    $o .= "      <th class=colname>TIMESTAMP</th>\n";
    $o .= "      <th class=colname>TRANSCRIPTION</th>\n";
    $o .= "  </tr>\n";
    $x=0;

    while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $TranscriptId=$row['TranscriptId'];
        $RecordingId=$row['RecordingId'];
        $TrackId=$row['TrackId'];
        $SecondsIn=$row['SecondsIn'];
        $Transcription=$row['Transcription'];
        $UserId=$row['UserId'];
        $Identifier = FetchLookup("Identifier", "tracks", "TrackId", $TrackId);
        // Set the block length for the call to playBlock(myTimeStamp, myLength)...
        $myLength = 5;
        if ($x == ($numberall - 1)) { $myLength = $LastBlockLength; }
        // Create Output Row...
        $rowclass='row' . ($x%2);        // Change Background color for each alternate row...

        // If there is a keyword in the session vars, and it occurs in the transcription block, highlight the row.
        if($_SESSION['kw'] && strpos($Transcription, $_SESSION['kw']) > 0):
           $rowclass = "hot";
        endif;

        $o .= "      <tr class='row0'>\n";
        // This if clause provides for a one second overlap between adjacent transcription block links,
        // in case the block splits a word in the middle.
        if ($x > 0) :
            $o .= "        <td><a href='#' onClick='playBlock($SecondsIn-1, $myLength+1);'>Listen @ " . gmdate("H:i:s", $SecondsIn) . "</a></td>\n";
        else:
            $o .= "        <td><a href='#' onClick='playBlock($SecondsIn, $myLength);'>Listen @ " . gmdate("H:i:s", $SecondsIn) . "</a></td>\n";
        endif;
        // This turns each word of $MinSearchWordLength into a link that fires a keyword search.
        $l = BuildLinks($Transcription, " ", "showRecordKeyword");
        $o .= "       <td>$l </td>\n";
        $o .= "      </tr>\n";
        $x++;
    endwhile;
    // Close Table...
    $o .= "  </table>\n";
    $o .= "  <br><a class=b1 href='#' onClick='clearDiv(\"annotations\");'>Close Transcription</a> &nbsp; \n";
    if ($myUser > 0) :
        $o .= "  <a href='#' class='b1' onClick='showTranscriptsEntry($TrackId, $myUser, \"$Identifier\", true)'>Edit Transcription</a>\n";
    endif;
    // The Superuser has UserId = 1, so they can delete the transcript records...
    if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1)) :
        $o .= "  &nbsp; <a href='#' class='b1' onClick='delRecord(\"recordView\",\"TR=$TrackId&U=$UserId\"),'>Delete Transcription</a>\n";
    endif;
    $o .= "  <div class=hbar></div>\n";
    $o .= "</div>\n";
endif;
?>