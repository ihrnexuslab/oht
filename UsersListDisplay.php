<?php
  if ($numberall==0) :
      $b .= "<h3>No User Records Found!</h3>\n";
  elseif ($numberall>0) :
      $b .= "<div class=centered>\n";
      $b .= "  <table rules=rows style='width: 75%;'>\n";
      $b .= "    <tr class=tblname>\n";
      $b .= "      <td colspan=4>$Title </td>\n";
      $b .= "    </tr>\n";
      $b .= "    <tr>\n";
      $b .= "      <th class=colname>NAME</th>\n";
      $b .= "      <th class=colname>E-MAIL</th>\n";
      $b .= "      <th class=colname colspan='2' align='center'>DATABASE FUNCTIONS</th>\n";
      $b .= "  </tr>\n";
      $x=0;
      while ($x<$numberall):
             // Retreive Data and put it in Local Variables for each Row...
             $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
             $UserId=$row['UserId'];
             $UserName=$row['UserName'];
             $UserEmail=$row['UserEmail'];
             // Create Output Row...
             $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
             $b .= "      <tr class=$rowclass>\n";
             $b .= "        <td>$UserName </td>\n";
             $b .= "        <td>$UserEmail </td>\n";
             $b .= "        <td align='center'><a href='#' onClick='showUserView($UserId);'>View Record</a></td>\n";
             $b .= "        <td align='center'><a href='#' onClick='showUserEntry($UserId);'>Edit Record</a></td>\n";
             $b .= "      </tr>\n";
             $x++;
      endwhile;
      // Close Table...
      $b .= "  </table>\n";
      $b .= "</div>\n";
  endif;
?>