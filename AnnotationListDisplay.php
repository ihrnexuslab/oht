<?php
  $o = "";
  if ($numberall==0) :
      $o .= "<h3>No Annotations for $myTrack!</h3>\n";
  elseif ($numberall>0) :
      $o .= "<div class=centered>\n";
      $o .= "  <table rules=rows style='width: 100%; font-size: 8.25pt;'>\n";
      $o .= "    <tr class=tblname>\n";
      $o .= "      <td colspan=5>$Title </td>\n";
      $o .= "    </tr>\n";
      $o .= "    <tr>\n";
      $o .= "      <th colspan=2 class=colname align='center'>ACTIONS</th>\n";
      $o .= "      <th class=colname>TYPE</th>\n";
      $o .= "      <th class=colname>DESCRIPTION</th>\n";
      $o .= "      <th class=colname>KEYWORDS (Click to Search)</th>\n";
      $o .= "  </tr>\n";
      $x=0;
      while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $AnnotationId=$row['AnnotationId'];
        $TrackId=$row['TrackId'];
        $AnnotationTypeId=$row['AnnotationTypeId'];
        $SecondsIn=$row['SecondsIn'];
        $SecondsOut=$row['SecondsOut'];
        $Description=$row['Description'];
        $Keywords=$row['Keywords'];
        $UserId=$row['UserId'];
        // Create Output Row...
        $rowclass='row' . ($x%2);        // Change Background color for each alternate row...

        // If there is a keyword in the session vars, and it occurs in the annotation, highlight the annotation.
        if($_SESSION['kw']) :
           $kw = $_SESSION['kw'];
           if ((strpos($Annotation, $kw) > 0) || (strpos($Description, $kw) > 0) || (strpos($KeyWords, $kw) > 0)) :
              $rowclass = "hot";
           endif;
        endif;
        $o .= "      <tr class=$rowclass>\n";
        $o .= "        <td align='center'>\n";
        // Determine whether the Annotation has an end point; if it does, send it with the call to jumpTo...
        if (!is_null($SecondsOut) && $SecondsOut > $SecondsIn):
            $o .= "          <a href='#' onClick='jumpTo($SecondsIn, $SecondsOut);'>Listen @ " . gmdate("H:i:s", $StartTime + $SecondsIn) . "</a>\n";
        else :
            $o .= "          <a href='#' onClick='jumpTo($SecondsIn);'>Listen @ " . gmdate("H:i:s", $StartTime + $SecondsIn) . "</a>\n";
        endif;
        $o .= "        </td>\n";
        $o .= "        <td align='center'>\n";
        if ($uData['IsAdmin'] == 1 || $uData['AnnotatesAll'] == 1 || $uData['AnnotatesOwn'] == 1 || $UserId == $myUser) :
            $o .= "          &nbsp; <a href='#' onClick='editAnnotation($AnnotationId);'>Edit</a>\n";
        endif;
        // The Superuser has UserId = 1, so they can delete the record...
        if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1) || $UserId == $myUser) :
            $o .= "          <br><a href='#' onClick='delRecord(\"annotations\",\"A=$AnnotationId\"; fetchAnnotationMarkers($TrackId););'>Delete</a>\n";
        endif;
        $o .= "        </td>\n";
        $o .= "        <td align='center'>" . FetchLookup("AnnotationType", "annotationtype", "AnnotationTypeId", $AnnotationTypeId) . " </td>\n";
        // This turns each word into a link that fires a keyword search.
        $l = BuildLinks($Description, " ", "showRecordKeyword");
        $o .= "       <td>$l</td>\n";
        // Keywords are stored as a comma-separated list.
        $l = BuildLinks($Keywords, ", ", "showRecordKeyword");
        $o .= "       <td>$l</td>\n";
        $o .= "      </tr></div>\n";
        $x++;
    endwhile;
    // Close Table...
        $o .= "      <tr class=row2>\n";
        $o .= "        <td colspan=5 class=colname>The green vertical bars mark locations of existing annotations.</td>\n";
        $o .= "      </tr>\n";
    $o .= "    </table>\n";
    $o .= "    <div class=hbar></div>\n";
//    $o .= "    <br><a class='b1' href='#' onClick='hidePlayer();'>Close Audio</a>\n";
  endif;
?>