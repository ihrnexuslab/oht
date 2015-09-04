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
        $Identifier = $row['Identifier'];
        $TrackId = $row['TrackId'];
        $SecondsIn = $row['SecondsIn'];

        // Create Output Row...
        $o .= "      <tr class=0>\n";
        $o .= "        <td>$Title </td>\n";
        $o .= "        <td class=center><a href='#' onClick='showRecordView($RecordingId);'>Metadata</a> | <a href='#' onClick='loadAudio(\"$Identifier\",$TrackId);'>Listen</a></td>\n";
        $o .= "      </tr>\n";
        $x++;
    endwhile;
    // Close Table...
    $o .= "    </table>\n";
    $o .= "</div>\n";
endif;
?>