<?php
  if ($numberall==0) :
      $o .= "<h3>No Records Found!</h3>\n";
   elseif ($numberall>0) :
      $o .= "<div class=centered>\n";
      $o .= "  <table rules=rows style='width: 100%; font-size: 8pt;'>\n";
      $o .= "    <tr class=tblname>\n";
      $o .= "      <td colspan=2>$Title </td>\n";
      $o .= "    </tr>\n";
      $o .= "    <tr>\n";
      $o .= "      <th class=colname>DESCRIPTION</th>\n";
      $o .= "      <th class=colname>ACTIONS</th>\n";
      $o .= "  </tr>\n";
      $x=0;
      while ($x<$numberall):
             // Retreive Data and put it in Local Variables for each Row...
             $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
             $permissions = array();
             $CompilationId=$row['compilation2user.CompilationId'];
             $Description=$row['compilations.Description'];
             $UserId = $row['compilation2user.UserId'];

             // This array will get passed in the link, so the showRecordings javascript routine can copy them to a javascript variable.
             $permissions['IsAdmin'] = $row['compilation2user.IsAdmin'];
             $permissions['CanUpload'] = $row['compilation2user.CanUpload'];
             $permissions['CanAnnotate'] = $row['compilation2user.CanAnnotate'];
             $permissions['CanDownload'] = $row['compilation2user.CanDownload'];
             $permissions['CanAdd'] = $row['compilation2user.CanAdd'];
             $permissions['CanModify'] = $row['compilation2user.CanModify'];

             // Create Output Row...
             $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
             $o .= "      <tr class=$rowclass>\n";
             $o .= "        <td>$Description </td>\n";
             $o .= "        <td align='center'>\n";
             $o .= "          <a href='#' onClick='showRecordings($CompilationId, $permissions);'>List Recordings</a>\n";
             if ($UId > 0 || $IsAdmin == 1 || $permissions['IsAdmin'] == 1):
                 $o .= " &nbsp; <a href='#' onClick='showCompEntry($CompilationId, $mySet);'>Edit Description</a>\n";
             endif;
             $o .= "        </td>\n";
             $o .= "      </tr>\n";
             $x++;
      endwhile;
      // Close Table...
      $o .= "  </table>\n";
      $o .= "  <br><a class=b1 href='#' onClick='clearDiv(\"recordView\");'>Close Compilations List</a> &nbsp; \n";
      $o .= "  <div class=hbar></div>\n";
      $o .= "</div>\n";
  endif;
?>