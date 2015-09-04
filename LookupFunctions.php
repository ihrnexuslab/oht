<?
//---------------------------------------------------------------------------------------    
function isLoggedIn()
   {
     if($_SESSION['valid']) return true;
     return false;
   }
 
//---------------------------------------------------------------------------------------    
function BuildLinkedListsJavaScript($Table1,$Key1,$Key1Cont,$Table2,$Key2,$Key2Cont,$Key2Val)  {
    global $conn; 
    $j = "<script type=\"text/javascript\">\n";
    $j .= "<!--\n\n";
    $j .= "function Set" . $Key1Cont . "Options(chosen)\n"; 
    $j .= "  /* ------------------------------------------------------------------------------------------------\n";
    $j .= "     This JavaScript routine links two list boxes, and is fired when the user changes the first one. \n";
    $j .= "     ------------------------------------------------------------------------------------------------ */\n";
    $j .= "  {\n";
    $j .= "   var selbox = document.form.$Key2;\n\n";
    $j .= "   selbox.options.length = 0;\n";
    $j .= "   if (chosen == \" \") {\n";
    $j .= "       selbox.options[selbox.options.length] = new Option('Select a Major Category first',' ');\n";
    $j .= "   }\n";

    $query1 = "SELECT $Key1 FROM $Table1 ORDER BY $Key1Cont";
    $result1 = mysqli_query($conn, $query1);
    $number1 = mysqli_num_rows($result1);
    $i=0;
    while ($i<$number1) :
           $row = mysqli_fetch_array($result1, MYSQLI_ASSOC);
           $Key = $row[$Key1]; 
           $j .= "   if (chosen == \"$Key\") {\n";
           $j .= "       selbox.options[selbox.options.length] = new Option('Select...','');\n";

           $query2 = "SELECT $Key2, $Key2Cont FROM $Table2 WHERE $Key1 = '$Key' ORDER BY $Key2";
//  echo "In BuildLinkedListsJavaScript, query2=$query2<br>\n";
           $result2 = mysqli_query($conn, $query2);
           $number2 = mysqli_num_rows($result2);
           $x=0;
           while ($x<$number2) :
                  $row = mysqli_fetch_array($result1, MYSQLI_ASSOC);
                  $K2 = $row[$Key2]; 
                  $K2Cont=$row[$Key2Cont];
                  $sel = "";
                  if($K2 == $Key2Val) {$sel = ", true";}
                  $j .= "       selbox.options[selbox.options.length] = new Option('$K2Cont','$K2'" . $sel .");\n";
                  $x++;
           endwhile;
           $j .= "   }\n";
           $i++;
    endwhile;
    $j .= "}\n";
    $j .= "//  End \n";
    $j .= "-->\n";
    $j .= "</script>\n";
    $b = mysqli_free_result($result1);
    $b = mysqli_free_result($result2);
    return $j;
   }

//----------------------------------------------------------------------------
function MakeList ($myFieldName, $myTable, $myId, $myItem, $myWhere, $myOrder, $mySelected, $Change)
// Using the the MakeList function:
// echo MakeList ("myFieldName", "myTable", "myId", "myItem", "myWhere", "MyOrder", mySelected);
// WHERE:
//  myFieldName is the name of the field on the form - usually the same name as the field in the table you're adding to...
//  myTable is the name of the LOOKUP TABLE
//  myId is the name of the KEY FIELD IN THE LOOKUP TABLE - e.g. if the table is Sites, this value would be "SiteId"...
//  myItem is the name of the FIELD FROM THE LOOKUP TABLE TO DISPLAY - e.g. if the table is Sites, this value would be "SiteNme"...
//  myWhere is the WHERE clause of the mysqli syntax, but without the word " WHERE "...
//  myOrder is the name of the sort order field.  If you don't want an order, use ""...
//  mySelected is the value you want selected in a drop-down list.  If none are pre-chosen, use ""...
//  Change is a boolean value, which indicates whether to add the "onchange" line in the select statement...

{  global $conn;
   $retval = "";
   // Drop-down list
   $myChange = "";
   if($Change == True){$myChange = " onchange='Set" . ucfirst($myItem) . "Options(document.form." . $myFieldName. ".options[document.form." . $myFieldName. ".selectedIndex].value)';";}
   $retval .= "          <select name=\"" . $myFieldName . "\"" . $myChange . ">\n";
   if($Change==true):
      $retval .= "          <option value=\"\">Select this first...</option>\n";
   else:
      $retval .= "          <option value=\"\">Select...</option>\n";
   endif;
   $Where = "";
   if (strlen($myWhere)>0) {$Where = " WHERE " . $myWhere;}
   $Order = "";
   if (strlen($myOrder)>0) {$Order = " ORDER BY " . $myOrder;}
   $q = "select " . $myId . "," . $myItem . " from " . $myTable . $Where . $Order;

 // echo "In MakeList, q=$q<br>\n";
   $result = mysqli_query($conn, $q);
   $numrecs = mysqli_num_rows($result);
   if ($numrecs>0) :
      $x=0;
      while ($x<$numrecs) :
         $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         $Id = $row[$myId]; 
         $Item = $row[$myItem]; 
         if ($Id == $mySelected) :
         $retval .= "          <option value=\"" . $Id . "\" SELECTED>" . $Item . "</option>\n"; 
         else:
         $retval .= "          <option value=\"" . $Id . "\">" . $Item . "</option>\n"; 
         endif;
             $x++;
      endwhile; 
   endif; 
   $retval .= "          </select>\n";
   $b = mysqli_free_result($result);
   return $retval;
}

//----------------------------------------------------------------------------
function MakeLookup ($myFieldName, $myTable, $myId, $myItem, $myType, $myOrder, $mySelected)
// Using the the MakeLookup function:
// echo MakeLookup ("myFieldName", "myTable", "myId", "myItem", "myType", "myOrder", $mySelected);
// WHERE:
//  myFieldName is the name of the field on the form - usually the same name as the field in the table you're adding to...
//  myTable is the name of the LOOKUP TABLE
//  myId is the name of the KEY FIELD IN THE LOOKUP TABLE - e.g. if the table is Sites, this value would be "SiteId"...
//  myItem is the name of the FIELD FROM THE LOOKUP TABLE TO DISPLAY - e.g. if the table is Sites, this value would be "SiteName"...
//  myType is what you want; it must be one of these: "List", "Multi", "Check", or "Radio" -- List is most common.
//         Also, new types include "ChkYes" - makes a check box, "YesNo" - makes yes no radio buttons.  
//  myOrder is the name of the sort order field.  If you don't want an order, use "".
//  mySelected is the value you want selected in a drop-down list.  If none are pre-chosen, use "".  If you have a value
//             in the field already, then call the function with the variable that contains it; this routine will
//             add the 'selected' term to the instance when the item is found during the build loop.

{  global $conn;
   $retval = "";
   if ($myType == "ChkYes") :
       // echo "mySelected=$mySelected,  myFieldName=$myFieldName<br>\n";
       if ($myFieldName != "" && $mySelected == 1) :
           $retval .= "          <input type=checkbox name='" . $myFieldName . "' value='1' checked='checked'>\n"; 
       else : 
           $retval .= "          <input type=checkbox name='" . $myFieldName . "' value='1'>\n"; 
       endif;
       return $retval;
   endif;

   if ($myType == "YesNo") :
       // echo "mySelected=$mySelected,  myFieldName=$myFieldName<br>\n";
       if ($myFieldName != "") :
           if ($mySelected == 1): 
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='1' checked='checked'> Yes \n";
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='0'> No\n";
           elseif ($mySelected == 0): 
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='1'> Yes \n";
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='0' checked='checked'> No\n";
           else : 
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='1'> Yes \n";
               $retval .= "          <input type=radio name='" . $myFieldName . "' value='0'> No\n";
           endif;
       endif;
       return $retval;
   endif;

   switch ($myType) :
      case "List":
           // Drop-down list
           $retval .= "          <select name=\"" . $myFieldName . "\">\n"; 
           $retval .= "            <option value=\"\">Select One...</option>\n";
      break;

      case "Multi":
           // Multi-select list box...
           $retval .= "          <select name='" . $myFieldName . "' multiple size='6'>\n"; 
      break;
   endswitch;

   if (strlen($myOrder)>0):
       $q = "select " . $myId . "," . $myItem . " from " . $myTable . " ORDER BY " . $myOrder;
   else :
       $q = "select " . $myId . "," . $myItem . " from " . $myTable;
   endif;
   $result = mysqli_query($conn, $q);

   //echo "Query=$q<br>";

   $numrecs = mysqli_num_rows($result);
   if ($numrecs>0) :
      $x=0;
      while ($x<$numrecs) :
             $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
             $Id = $row[$myId]; 
             $Item = $row[$myItem]; 
             switch ($myType){
              case "List":
                if ($Id == $mySelected) :
                    $retval .= "            <option value='" . $Id . "' SELECTED>" . $Item . "</option>\n"; 
                else:
                    $retval .= "            <option value=\"" . $Id . "\">" . $Item . "</option>\n"; 
                endif;
              break;
              case "Multi":
                $retval .= "            <option value='" . $Id . "'>" . $Item . "</option>\n"; 
              break;
              case "Check":
                $retval .= "        <input type=checkbox name='" . $Id . "' value='" . $Id . "'>" . $Item . " <br>\n"; 
              break;
              case "Radio";
                $retval .= "        <input type=radio name='" . $Id . "' value='" . $Item . "'>" . $Item . " <br>"; 
              break;
             }
             $x++;
      endwhile; 
   endif; 
   if ($myType=="List" || $myType="Multi") { $retval .= "      </select>\n"; }
   $b = mysqli_free_result($result);
   return $retval;
}

//----------------------------------------------------------------------------
function MakeDropDown ($myFieldName, $myTable, $myKeyFld, $myLookupField, $myLookupVal, $myShowFld, $myOrder, $mySelected, $myChange)
// Using the the MakeDropDown function:
// echo MakeDropDown ("myFieldName", "myTable", "myKeyFld", "myLookupField", $myLookupVal, "myShowFld", "myOrder", $mySelected, $myChange);
// WHERE:
//  myFieldName is the name of the field on the form - usually the same name as the field in the table you're adding to...
//  myTable is the name of the LOOKUP TABLE
//  myKeyFld is the name of the KEY FIELD IN THE LOOKUP TABLE - e.g. if the table is Sites, this value would be "SiteId"...
//  myLookupField is the name of the field to use in a WHERE clause...
//  myLookupVal is the value of myLookupField to look for...
//  myShowFld is the name of the FIELD FROM THE LOOKUP TABLE TO DISPLAY - e.g. if the table is Sites, this value would be "SiteNme"...
//  myOrder is the name of the sort order field.  If you don't want an order, use "".
//  mySelected is the value you want selected in a drop-down list.  If none are pre-chosen, use "".
//  myChange is the fully formed line to execute in an onChange event.  If none, use "".

{  global $conn;
   $retval = "";
   $retval .= "          <select name=\"" . $myFieldName . "\" $myChange>\n"; 
   $retval .= "            <option value=\"\">Select...</option>\n";
   $where = "";
   if ($myLookupField != "" && $myLookupVal !="") {$where = "WHERE " . $myLookupField . " = '$myLookupVal'";}
   if (strlen($myOrder)>0):
       $q = "SELECT $myKeyFld, $myShowFld FROM $myTable $where ORDER BY $myOrder";
   else :
       $q = "SELECT $myKeyFld, $myShowFld FROM $myTable $where";
   endif;
   $result = mysqli_query($conn, $q);

  // echo "Query=$q<br>";

   $numrecs = mysqli_num_rows($result);
   if ($numrecs>0) :
      $x=0;
      while ($x<$numrecs) :
             $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
             $Id = $row[$myKeyFld]; 
             $Item = $row[$myShowFld]; 
             if ($Id == $mySelected) :
                 $retval .= "            <option value=\"" . $Id . "\" SELECTED>" . $Item . "</option>\n"; 
             else:
                 $retval .= "            <option value=\"" . $Id . "\">" . $Item . "</option>\n"; 
             endif;
             $x++;
      endwhile; 
   endif; 
   $retval .= "      </select>\n";
   $b = mysqli_free_result($result);
   return $retval;
}

//----------------------------------------------------------------------------
function FetchLookup($myFieldName, $myTable, $myKey, $myKeyVal)
// Using the FetchLookup function:
// echo FetchLookup("myFieldName", "myTable", "myKey", $myValue);
// WHERE:
//  myFieldName is the name of the field to be returned...
//  myTable is the name of the LOOKUP TABLE.
//  myKey is the name of the key field in the lookup table.
//  $myKeyVal is the value of the key to search for; pass is a real value, not a sting!
//  e.g.  echo FetchLookup("SiteName","Sites","SiteId",$SiteId); 
//  will fetch the site name from the Sites table associated with the value of $SiteId.
{  global $conn;
   $retval = "";
   $q = "SELECT " . $myFieldName . " FROM " . $myTable . " WHERE " . $myKey . " = '" . $myKeyVal . "'";

//   echo "Query = $q<br>\n";

   $result = mysqli_query($conn, $q);
   $numrecs = mysqli_num_rows($result);
   if ($numrecs>0) :
       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
       $retval = $row[$myFieldName]; 
   else :
       $retval = " ";
   endif;
   $b = mysqli_free_result($result);
   return $retval;
}

//----------------------------------------------------------------------------
function SetNavigationRecords($KeyFld, $KeyVal, $myTable, $SrchFld, $SrchVal)
// Using the SetNavigationRecords function:
// $k = SetNavigationRecords("KeyFld", $KeyVal, "myTable", "SrchFld", $SrchVal);
// WHERE:
//       KeyFld = the name of the key field in the table we are looking up....
//       $KeyVal = the current value of the primary key...
//       myTable = the name of the lookup table...
//       SrchFld = the name of the field in the table to order the search on...
//       $SrchVal = the current value of the field we will order on...

// In the $k array, even numbered indices will return with a null value or " disabled",
// which will determine the button style to use from the style sheet.  Currently set to 
// have disabled buttons not appear at all (everything is set to the background color, so
// while the button is actually on the form it will be invisible.  Odd numbered indices
// in $k contain the primary key value to retreive if the button is pressed.

{  global $conn; 
   $k=array();
   // First Record...With Special Case If We're Sitting On The First Record...
   $npquery="SELECT $KeyFld FROM $myTable ORDER BY $SrchFld ASC LIMIT 1";
   $npresult = mysqli_query($conn, $npquery);
   $nprows = mysqli_num_rows($npresult);
   if($nprows>0) :
      if(mysqli_result($npresult, 0)==$KeyVal) :
        $k[0] = "disabled";
        $k[1] = $KeyVal;
      else :
        $k[0] = "button";
        $k[1] = mysqli_result($npresult, 0);
      endif;
   else :
      $k[0] = "disabled";
      $k[1] = $KeyVal;
   endif;

   // Previous Record...
   $npquery="SELECT $KeyFld FROM $myTable WHERE $SrchFld < '$SrchVal' ORDER BY $SrchFld DESC LIMIT 1";
   $npresult = mysqli_query($conn, $npquery);
   $nprows = mysqli_num_rows($npresult);
   if($nprows>0) :
      $k[2] = "button"; 
      $k[3] = mysqli_result($npresult, 0);
   else :
      $k[2] = "disabled";
      $k[3] = $KeyVal;
   endif;

   // Next Record...
   $npquery="SELECT $KeyFld FROM $myTable WHERE $SrchFld > '$SrchVal' ORDER BY $SrchFld ASC LIMIT 1";
   $npresult = mysqli_query($conn, $npquery);
   $nprows = mysqli_num_rows($npresult);
   if($nprows>0) :
      $k[4] = "button";
      $k[5] = mysqli_result($npresult, 0); 
   else :
      $k[4] = "disabled";
      $k[5] = $KeyVal;
   endif;

   // Last Record...With Special Case If We're Sitting On The Last Record...
   $npquery="SELECT $KeyFld FROM $myTable ORDER BY $SrchFld DESC LIMIT 1";
   $npresult = mysqli_query($conn, $npquery);
   $nprows = mysqli_num_rows($npresult);
   if($nprows>0) :
      if(mysqli_result($npresult, 0)==$KeyVal) :
        $k[6] = "disabled";
        $k[7] = $KeyVal;
      else :
        $k[6] = "button";
        $k[7] = mysqli_result($npresult, 0);
      endif;
   else :
      $k[6] = "disabled";
      $k[7] = $KeyVal;
   endif;
 //  print_r($k);
   $b = mysqli_free_result($npresult);
   return($k);
}

//-----------------------------------------------------------------------------------------
// The BuildLinkedDropdown function that follows is based on the following query structure:
// $select = "SELECT Buildings.Building, Rooms.RoomNum, Rooms.RoomId ";
// $from = "FROM Buildings INNER JOIN Rooms ON Buildings.BuildingId = Rooms.BuildingId ";
// $where = "WHERE Rooms.RoomId=1";
// $order = "ORDER BY Buildings.Building, Rooms.RoomNum";

// The function builds a drop down list by joining fields from two related tables, showing
// one field from each of the tables, and setting up a return value that is the primary key
// field in the joined table.

// Values passed in are as follows:
// $t1 = Name of the first (outer) table
// $f1 = Name of the field to get from the outer table
// $t2 = Name of the second (inner) table
// $f2 = Name of the field to get from the inner table
// $k = Name of the second field to get from the inner table; this should be the inner table primary key field! 
// $j = Name of the common field
// $s = A value of the inner table's primary key to be selected.

function BuildLinkedDropdown ($t1,$f1,$t2,$f2,$k,$j,$s)
{   global $conn; 
    $select = "SELECT $t1.$f1, $t2.$f2, $t2.$k ";
    $from = "FROM $t1 INNER JOIN $t2 ON $t1.$j = $t2.$j ";
    $order = "ORDER BY $t1.$f1, $t2.$f2";
    $retval  = "          <select name='$k'>\n";
    $retval .= "            <option value=\"\">Select...</option>\n";

    $query1 = $select.$from.$order;

//  echo "Query=$query1<br>\n";

    $result1 = mysqli_query($conn, $query1);
    $numrecs = mysqli_num_rows($result1);
    if ($numrecs>0) :
        $i=0;
        while ($i<$numrecs) :
           $v1 = mysqli_result($result1,$i,"$f1"); 
           $v2 = mysqli_result($result1,$i,"$f2"); 
           $ky = mysqli_result($result1,$i,"$k"); 
           if ($ky == $s) :
               $retval .= "            <option value='$ky' SELECTED>$v1 $v2</option>\n"; 
           else:
               $retval .= "            <option value='$ky'>$v1 $v2</option>\n"; 
           endif;
           $i++;
        endwhile; 
   endif; 
   $retval .= "          </select>\n";
   $b = mysqli_free_result($result1);
   return $retval;
}

//-----------------------------------------------------------------------------------------
// The function returns two fields from two related tables, showing one 
// field from each of the tables, based on a search field in the second table.

// SELECT Buildings.Building, Rooms.RoomNum
// FROM Buildings INNER JOIN Rooms ON Buildings.BuildingId = Rooms.BuildingId
// WHERE (((Rooms.RoomId)=1));

// Values passed in are as follows:
// $t1 = Name of the first (outer) table
// $f1 = Name of the field to get from the outer table
// $t2 = Name of the second (inner) table
// $f2 = Name of the field to get from the inner table
// $k = field to search on in second table
// $j = Name of the common field
// $w = A value of the inner table's primary key to be selected.

function FetchLinkedFields ($t1,$f1,$t2,$f2,$k,$j,$w)
{  global $conn; 
   $select = "SELECT $t1.$f1, $t2.$f2 ";
   $from = "FROM $t1 INNER JOIN $t2 ON $t1.$j = $t2.$j ";
   $where = "WHERE $t2.$k = '$w'";
   $query1 = $select.$from.$where;

// echo "Query=$query1<br>\n";

   $result1 = mysqli_query($conn, $query1);
   $num1 = mysqli_num_rows($result1);
   if ($num1 == 0) :
       $retval="";
   else:
       $v1 = mysqli_result($result1,0,"$f1"); 
       $v2 = mysqli_result($result1,0,"$f2"); 
       $retval = "$v1 : $v2";
   endif;
   $b = mysqli_free_result($result1);
   return $retval;
}

//-----------------------------------------------------------------------------------------
function FetchTracks($myRecordingId, $HotTrack)
{  global $conn; 
   global $myUser;
   global $IsAdmin;
   global $AdminsCanDelete;
   $bq = "SELECT TrackId,FilePath,Identifier,Seconds FROM tracks WHERE RecordingId = '$myRecordingId' ORDER BY Identifier";
   $resultall = mysqli_query($conn, $bq);
   $numberall = mysqli_num_rows($resultall);
   if ($numberall==0) :
       $r = "<b>No Tracks Found!</b>\n";
   elseif ($numberall>0) :
       $r .= "<form name='myTracks' id='myTracks'>\n";
       $r .= "  <table rules=rows style='width: 95%; font-size: 8.75pt;'>\n";
       $r .= "    <tr>\n";
       $r .= "      <th class='colname' style='width: 20%'>Time</th>\n";
       $r .= "      <th class='colname'>Track</th>\n";
       $r .= "      <th class='colname' align='center' valign='middle'>Duration</th>\n";
       $r .= "      <th class='colname'>Actions</th>\n";
       $r .= "  </tr>\n";

       $x=0;
       $startTime = 0;
       while ($x<$numberall):
              // Retreive Data and put it in Local Variables for each Row...
              $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
              $TrackId = $row['TrackId'];
              $FilePath = $row['FilePath'];
              $Identifier = $row['Identifier'];
              $Seconds = $row['Seconds'];
              $myAudioPath = $FilePath . $Identifier;
              // Create Output Row...
              if ($TrackId == $HotTrack) :
                  $r .= "      <tr class=hot>\n";
              else :
                  $r .= "      <tr class=row2>\n";
              endif;
              $r .= "        <td class=center>" . gmdate("H:i:s", $startTime) . " - " . gmdate("H:i:s", $startTime+$Seconds) . "</td>\n";
              $r .= "        <td>$Identifier </td>\n";
              $r .= "        <td align='center' valign='middle'>" . gmdate("H:i:s", $Seconds) . "</td>\n";
              $r .= "        <td class=center>\n";
              $l = "Listen";
              if ($myUser > 0) { $l .= "/Annotate"; }
              $r .= "            <a href='#' onClick='controlLoader($x, 0);'>$l</a> &nbsp;\n";
              $HasBlocks = (CountTranscriptionBlocks($TrackId, 0)>0) ? 1 : 0;
              if ($HasBlocks == true) :
                  $r .= "            <a href='#' onClick='controlLoader($x, 1);'>Transcriptions</a>\n";
              endif;
              if ($myUser > 0) :
                  // If CountTranscriptionBlocks returns > 0, then the transcription has been initialized.
                  // So just open the transcription data entry form, otherwise, initialize the transcription blocks.
                  // The transcription data entry form will be opened after the transcription blocks are initialized;
                  // that call is made at the bottom of the initialization routine
                  $HasBlocks = (CountTranscriptionBlocks($TrackId, $myUser)>0) ? 1 : 0;
                  $r .= "     &nbsp; <a href='#' onClick='showTranscriptsEntry($TrackId, $myUser, \"" . $myAudioPath . "\", $HasBlocks, $startTime)'>Transcribe</a>\n";
              endif;
              // The Superuser has UserId = 1, so they can delete the record...
              if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1)) :
                  $r .= "     &nbsp; <a href='#' style='color:red' onClick='delRecord(\"recordView\",\"T=$TrackId\");'><span style='color:red'>Delete Track</span></a>\n";
              endif;
              $r .= "        </td>\n";
              $r .= "        <input type='hidden' name='trackPath[$x]' value = '$myAudioPath'>\n";
              $r .= "        <input type='hidden' name='trackId[$x]' value = $TrackId>\n";
              $r .= "        <input type='hidden' name='trackStartTime[$x]' value = $startTime>\n";
              $r .= "      </tr>\n";
              $startTime = $startTime + $Seconds;
              $x++;
       endwhile;
       // Close Table...
       $r.= "    </table>\n";
       $r .= "</form>\n";
   endif;

   return $r;
}

//-----------------------------------------------------------------------------------------
// CountTranscriptionBlocks($myTrackId, $myUserId) -- returns the number of transcription
//                          blocks for the given track and user.  If a 0 is passed as the
//                          $myUserId, then it will count the blocks regardless of the user
//-----------------------------------------------------------------------------------------
function CountTranscriptionBlocks($myTrackId, $myUserId)
{  global $conn; 
   $bq = "SELECT TranscriptId FROM transcripts WHERE TrackId = '$myTrackId'";
   if ($myUserId > 0) { $bq .= " AND UserId = '$myUserId'"; }
   $resultall = mysqli_query($conn, $bq);
   return mysqli_num_rows($resultall);
}

//-----------------------------------------------------------------------------------------
// CountTracks($myRecordingId) -- returns the number of tracks for the given recording.
//-----------------------------------------------------------------------------------------
function CountTracks($myRecordingId)
{  global $conn; 
   $bq = "SELECT TrackId FROM tracks WHERE RecordingId = '$myRecordingId'";
   $resultall = mysqli_query($conn, $bq);
   return mysqli_num_rows($resultall);
}

//-----------------------------------------------------------------------------------------
// FetchPermissions($myCId, $myUserId) -- returns the permissions for the user on the
//                                        compilation.
//-----------------------------------------------------------------------------------------
function FetchPermissions($myCId, $myUserId)
{  global $conn; 
   $bq = "SELECT IsAdmin, CanUpload, CanAnnotate, CanDownload, CanAdd, CanModify FROM compilation2user WHERE CompilationId = '$myCId' AND UserId = '$myUserId'";
   $resultall = mysqli_query($conn, $bq);
   return mysqli_num_rows($resultall);
}

//-----------------------------------------------------------------------------------------
// function BuildLinks($myText, $mySep, $myDest) -- Builds a string of links
//          based on the text value $myText, by exploding $myText by the
//          separator $mySep.  If each word is >= the minimum search word
//          length (the global $MinSearchWordLength), then a link to
//          $myDest is built, otherwise, the word is included without a link.
// USAGE EXAMPLE:
//          $l = BuildLinks($Description, ", ", "showRecordView");
//-----------------------------------------------------------------------------------------

function BuildLinks($myText, $mySep, $myDest)
{ global $MinSearchWordLength;
  $kw = explode($mySep,$myText);
  $l = "";
  foreach ($kw as $kwrd):
     if (strlen($kwrd) >= $MinSearchWordLength) :
         $k = str_replace(array(",","?","!",";",":"), "", $kwrd);
         $l .= "<a href='#' onClick='$myDest(\"" . urlencode($k) . "\");'>" . stripslashes($kwrd) . "</a> ";
     else :
         $l .= stripslashes($kwrd) . " ";
     endif;
  endforeach;
  if (strlen($l) > 0) { $l = rtrim($l, $mySep); }
  return $l;
}

?>