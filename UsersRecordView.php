<?php
  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $ny = array("No", "Yes");
  if (isset($_GET['U'])) { $UserId=$_GET['U']; }
  $Title = 'Users Table:';
  if ($UserId!="") { $where = " WHERE UserId = '$UserId'"; }

  $bareQuery = "SELECT * FROM users ";
  $querysearch = $bareQuery.$where;
  $resultsearch = mysqli_query($conn, $querysearch);
  $numbersearch = mysqli_num_rows($resultsearch);

  if ($numbersearch==0) :
      $o = "<h3>User Record Not Found!</h3>\n";
      $o .= "<a class=b1 href='#' onClick='clearDiv(\"recordView\");>Close</a> &nbsp; \n";
      if ($uData['IsAdmin'] == 1) :
          $o .= "<a class=b1 href='#' onClick='showUserEntry(0);'>Add New User</a>\n";
      endif;
  else :
      // Retreive data and put it in local variables...

      $row = mysqli_fetch_array($resultsearch, MYSQLI_ASSOC);
      $UserId=$row['UserId'];
      $UserName=$row['UserName'];
      $UserAddress=$row['UserAddress'];
      $UserPhone=$row['UserPhone'];
      $UserMobilePhone=$row['UserMobilePhone'];
      $UserFax=$row['UserFax'];
      $UserEmail=$row['UserEmail'];
      $UserURL=$row['UserURL'];
      $UserPwReminder=$row['UserPwReminder'];
      $CountryID=$row['CountryID'];
      $IsAdmin=$row['IsAdmin'];
      $CanUpload=$row['CanUpload'];
      $AnnotatesOwn=$row['AnnotatesOwn'];
      $AnnotatesAll=$row['AnnotatesAll'];
      $CanDownload=$row['CanDownload'];
      $CanAdd=$row['CanAdd'];
      $CanModify=$row['CanModify'];
      $EnteredBy=$row['EnteredBy'];
      $DateEntered=$row['DateEntered'];

      // Format the output table...
      $o .= "<div class=centered>\n";
      $o .= "   <table class='rv' style='width: 100%; font-size: 8.25pt;'>\n";
      $o .= "     <tr class=row1>\n";
      $o .= "      <td colspan=6 class=colname>$UserName's Full User Record:</td>\n";
      $o .= "     </tr>\n";
      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>Name:</td>\n";
      $o .= "       <td class=data>$UserName </td>\n";
      $o .= "       <td class=fldname>Address:</td>\n";
      $o .= "       <td class=data>$UserAddress </td>\n";
      $o .= "       <td class=fldname>Country:</td>\n";
      $o .= "       <td class=data>" . FetchLookup("Name", "countries", "CountryID", $CountryID) . " </td>\n";
      $o .= "     </tr>\n";

      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>Phone:</td>\n";
      $o .= "       <td class=data>$UserPhone </td>\n";
      $o .= "       <td class=fldname>Mobile:</td>\n";
      $o .= "       <td class=data>$UserMobilePhone </td>\n";
      $o .= "       <td class=fldname>Fax:</td>\n";
      $o .= "       <td class=data>$UserFax </td>\n";
      $o .= "     </tr>\n";

      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>E-mail:</td>\n";
      $o .= "       <td class=data>$UserEmail </td>\n";
      $o .= "       <td class=fldname>URL:</td>\n";
      $o .= "       <td class=data colspan=3>$UserURL </td>\n";
      $o .= "     </tr>\n";

      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>PW Prompt:</td>\n";
      $o .= "       <td class=data colspan=3>$UserPwReminder </td>\n";
      $o .= "       <td class=fldname>Is Admin?:</td>\n";
      $o .= "       <td class=data>$ny[$IsAdmin]</td>\n";
      $o .= "     </tr>\n";

      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>Uploads?:</td>\n";
      $o .= "       <td class=data>$ny[$CanUpload]</td>\n";
      $o .= "       <td class=fldname style='width: 130px'>Tags Own?:</td>\n";
      $o .= "       <td class=data>$ny[$AnnotatesOwn]</td>\n";
      $o .= "       <td class=fldname>Tags All?:</td>\n";
      $o .= "       <td class=data>$ny[$AnnotatesAll]</td>\n";
      $o .= "     </tr>\n";

      $o .= "     <tr class=row1>\n";
      $o .= "       <td class=fldname>Downloads?:</td>\n";
      $o .= "       <td class=data>$ny[$CanDownload]</td>\n";
      $o .= "       <td class=fldname>Can Add?:</td>\n";
      $o .= "       <td class=data>$ny[$CanAdd]</td>\n";
      $o .= "       <td class=fldname>Modify?:</td>\n";
      $o .= "       <td class=data>$ny[$CanModify]</td>\n";
      $o .= "     </tr>\n";

      $o .= "  </table><br>&nbsp;\n";
      $o .= "  <a class=b1 href='#' onClick='clearDiv(\"recordView\");'>Close</a> &nbsp; \n";
      $o .= "  <a class=b1 style='text-decoration: none' href='#' onClick='showUserEntry($UserId);'>Edit Record</a> &nbsp; \n";
      $o .= "  <a class=b4 style='text-decoration: none' href='#' onClick='showPWReset($UserId);'>Reset User's Password</a> &nbsp; \n";
      $o .= "  <hr>";
      $o .= "</div>\n";
  endif;
  echo $o;
  $o = "";
?>