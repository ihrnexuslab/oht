<?php
if ($numberall>0) :
    $o .= "<div class=centered>\n";
//    $o .= "  <h5 style='width: 100%; font-size: 8.25pt;'>$Title</h5>\n";
    $o .= "  <table style='width: 100%; font-size: 8.25pt;'>\n";
    $o .= "    <tr>\n";
    $o .= "      <td colspan=2 class='tblname' style='font-size: 8.25pt;'>$Title</td>\n";
    $o .= "    </tr>\n";
    $o .= "    <tr>\n";
    $o .= "      <th class='colname'>Title</th>\n";
    $o .= "      <th class='colname'>Actions</th>\n";
    $o .= "    </tr>\n";
    $x=0;

    while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $RecordingId = $row['RecordingId'];
        $Title = $row['Title'];
        $UserId = FetchLookup("UserId", "Recordings", "RecordingId", $RecordingId);
        $Publish = FetchLookup("Publish", "Recordings", "RecordingId", $RecordingId);
        if (strpos($bareQuery,'TrackId') > 0) :
            $TrackId = $row['TrackId'];
            $Identifier = " - " . FetchLookup("Identifier", "Tracks", "TrackId", $TrackId);
        else :
            $TrackId = 0;
            $identifier = "";
        endif;
        // We can show the record under three different conditions:
        // 1. It's Publish flag is set to 1 (true);
        // 2. The logged in user is the owner of the record;
        // 3. The logged in user is an administrator.
        If (($Publish == "1") || ($UserId == $myUser) || ($uData['IsAdmin'] == 1)) : 
           // Create Output Row...
           $o .= "      <tr class=0>\n";
           $o .= "        <td>$Title$Identifier</td>\n";
           $o .= "        <td class=center><a href='#' onClick='showRecordView($RecordingId, $TrackId);'>Metadata</a></td>\n";
           $o .= "      </tr>\n";
           $x++;
        endif;
    endwhile;
    // Close Table...
    $o .= "    </table><br>\n";
    $o .= "    <a href='#' class=b1 style='text-decoration:none; margin-top:.5em' onClick='clearDiv(\"keyWord\");'>Close List</a> &nbsp; \n";
    $o .= "</div>\n";
endif;
?>