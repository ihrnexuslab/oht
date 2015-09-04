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
             $CompilationId=$row['CompilationId'];
             $Description=$row['Description'];
             // Create Output Row...
             $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
             $o .= "      <tr class=$rowclass>\n";
             $o .= "        <td>$Description </td>\n";
             $o .= "        <td align='center'>\n";
             $o .= "          <a href='#' onClick='showRecordings($CompilationId);'>List Recordings</a>\n";
             if ($uData['CanUpload'] == 1 || $uData['IsAdmin'] == 1) :
                 $o .= "     &nbsp;<a href='#' onClick='showRecordEntry(0, $CompilationId);' style='margin-top: 1em; text-decoration:none'> Add a Recording</a> &nbsp;\n";
             endif;
             if ($UId > 0 || $IsAdmin == 1):
                 $o .= " &nbsp; <a href='#' onClick='showCompEntry($CompilationId, $mySet);'>Edit Description</a>\n";
             endif;
             // The Superuser has UserId = 1, so they can delete the record...
             if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1)) :
                 $o .= "     &nbsp; <a href='#' style='color:red' onClick='delRecord(\"recordView\",\"C=$CompilationId\");'><span style='color:red'>Delete</span></a>\n";
             endif;
             $o .= "        </td>\n";
             $o .= "      </tr>\n";
             $x++;
      endwhile;
      // Close Table...
      $o .= "  </table>\n";
      $o .= "  <br><a class=b1 href='#' onClick='clearDiv(\"recordView\");'>Close Compilations List</a> &nbsp; \n";
      if (isset($mySet)) { $o .= "  <div class=hbar></div>\n"; }
      $o .= "</div>\n";
  endif;

?>