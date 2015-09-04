<?php
  // Compilation2UserListDisplay.php

  if ($numberall==0) :
      $o = "<h3>No Users associated with this Compilation!</h3>\n";
  elseif ($numberall>0) :
      $o .= "<div class=centered>\n";
      $o .= "  <table class='rv' rules=rows style='width: 95%; font-size: 8pt;'>\n";
      $o .= "    <tr class=tblname>\n";
      $o .= "      <td colspan=7>$Title </td>\n";
      $o .= "    </tr>\n";
      $o .= "    <tr>\n";
      $o .= "      <th class=colname>USERID</th>\n";
      $o .= "      <th class=colname>ISADMIN</th>\n";
      $o .= "      <th class=colname>CANUPLOAD</th>\n";
      $o .= "      <th class=colname>CANANNOTATE</th>\n";
      $o .= "      <th class=colname>CANADD</th>\n";
      $o .= "      <th class=colname>CANMODIFY</th>\n";
      $o .= "      <th class=colname colspan='1' align='center'>DATABASE FUNCTIONS</th>\n";
      $o .= "    </tr>\n";
      $x=0;

      while ($x<$numberall):
             // Retreive Data and put it in Local Variables for each Row...
             $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
             $Compilation2UserId=$row['Compilation2UserId'];
             $CompilationId=$row['CompilationId'];
             $UserId=$row['UserId'];
             $IsAdmin=$row['IsAdmin'];
             $CanUpload=$row['CanUpload'];
             $CanAnnotate=$row['CanAnnotate'];
             $CanAdd=$row['CanAdd'];
             $CanModify=$row['CanModify'];

             // Create Output Row...
             $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
             $o .= "    <tr class=$rowclass>\n";
             $o .= "      <td>" . FetchLookup("UserName", "users", "UserId", $UserId) . " </td>\n";
             $o .= "      <td>$ny[$IsAdmin]</td>\n";
             $o .= "      <td>$ny[$CanUpload]</td>\n";
             $o .= "      <td>$ny[$CanAnnotate]</td>\n";
             $o .= "      <td>$ny[$CanAdd]</td>\n";
             $o .= "      <td>$ny[$CanModify]</td>\n";
             $o .= "      <td align='center'><a href='Compilation2userEntryForm.php?Compilation2UserId=$Compilation2UserId'>Edit Record</a></td>\n";
             $o .= "    </tr>\n";
             $x++;
      endwhile;
      // Close Table...
      $o .= "  </table><br>\n";
  endif;
  $o .= "  <a class='b1' href='#' onClick='processAjax(\"Compilation2userEntryForm.php\", \"columnLeft\");'>Add User</a> &nbsp; <a class=b1 href='#' onClick='clearDiv(\"recordView\");'>Close Users List</a>\n";
  $o .= "  <div class=hbar></div>\n";
  $o .= "</div>\n";
?>