<?php

  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  if (isset($_GET['TId'])) { $TrackId=$_GET['TId']; }
  if ($TrackId!="") :
      $where = " Where TrackId = '$TrackId'";
      $Title .= $where;
  endif;

  $bareQuery = "SELECT * FROM tracks ";
  $querysearch = $bareQuery.$where;
  $resultsearch = mysqli_query($conn, $querysearch);
  $numbersearch = mysqli_num_rows($resultsearch);

  if ($numbersearch==0) :
      $o = "<h3>Record Not Found!</h3>\n";
  elseif ($numbersearch>0) :
      // Retreive data and put it in local variables...
      $row = mysqli_fetch_array($resultsearch, MYSQLI_ASSOC);
      $TrackId=$row['TrackId'];
      $RecordingId=$row['RecordingId'];
      $FilePath = $row['FilePath'];
      $Identifier=$row['Identifier'];
      $Seconds=$row['Seconds'];
      $DateAdded=$row['DateAdded'];
      $UserId=$row['UserId'];

      // Format the output table...
      $o .= "<div class=centered><table class='rv' style='width: 90%; font-size: 8.25pt;' rules=groups>\n";
      if(strlen($Identifier) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Identifier") . ":</td>\n";
         $o .= "       <td class=data>$Identifier </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($Seconds != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Seconds") . ":</td>\n";
         $o .= "       <td class=data>$Seconds </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($UserId != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Uploaded By") . ":</td>\n";
         $o .= "       <td class=data>" . FetchLookup("UserName", "users", "UserId", $UserId) . " </td>\n";
         $o .= "     </tr>\n";
      endif;
        $o .= "     <tr class=row1>\n";
        $o .= "       <td colspan=2 class=colname>\n";
        $myAudioPath = $FilePath . $Identifier;
        $o .= "         <a class='b2' href='#' onClick='loadAudio(\"$myAudioPath\",$TrackId);'>Listen</a> &nbsp; \n";
        $o .= "         <a class='b2' href='#' onClick='toggleVis(\"recordView\");'>Hide</a> &nbsp; \n";
        // If the user is an Administrator, allow editing and uploading.
        // If not, allow editing their own tracks if they have editing rights.
        // And allow uploading a new recording if they have uploading rights.

        // With the new recording -> track record layout, it's not possible to edit the track, so the next line is commented out...
        // if (($uData['CanModify'] == 1 && $UserId == $myUser) || $uData['IsAdmin'] == 1) { 
             $o .= "         <a class='b2' href='#' onClick='showTrackEntry($TrackId, $RecordingId);'>Edit Track</a> &nbsp; \n"; }

        if (($uData['CanUpload'] == 1 && $UserId == $myUser) || $uData['IsAdmin'] == 1) { 
             $o .= "         <a class='b2' href='#' onClick='showTrackEntry(0, $RecordingId);'>Upload a New Track</a>\n"; }
        $o .= "       </td>\n";
        $o .= "     </tr>\n";
        $o .= "</table></div><br>&nbsp;\n";
  endif;
  echo $o;
?>