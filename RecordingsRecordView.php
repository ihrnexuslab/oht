<?php

  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }
      $IsAdmin = $uData['IsAdmin'];
  endif;

  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  require_once("LookupFunctions.php");

  if (isset($_GET['RId'])) { $RecordingId=$_GET['RId']; }
  if (isset($_GET['TId'])) { $HotTrack=$_GET['TId']; }
  if ($RecordingId!="") :
      $where = " Where RecordingId = '$RecordingId'";
  endif;

  $bareQuery = "SELECT * FROM recordings ";
  $querysearch = $bareQuery.$where;
  $resultsearch = mysqli_query($conn, $querysearch);
  $numbersearch = mysqli_num_rows($resultsearch);

  if ($numbersearch==0) :
      $o = "<h3>Record Not Found!</h3>\n";
  elseif ($numbersearch>0) :
      // Retreive data and put it in local variables...
      $row = mysqli_fetch_array($resultsearch, MYSQLI_ASSOC);
      $CompilationId=$row['CompilationId'];
      $RecordingId=$row['RecordingId'];

      $numTracks = CountTracks($RecordingId);

      $Title=$row['Title'];
      $Subject=$row['Subject'];
      $Description=$row['Description'];
      $Creator=$row['Creator'];
      $Source=$row['Source'];
      $Publisher=$row['Publisher'];
      $Date=$row['Date'];
      $FreeFormDate=$row['FreeFormDate'];
      $PermissionTypeId=$row['PermissionTypeId'];
      $AudioFormatId=$row['AudioFormatId'];
      $LangID=$row['LangID'];
      $Type=$row['Type'];
      $Coverage=$row['Coverage'];
      $Spatial=$row['Spatial'];
      $Keywords=$row['Keywords'];
      $DateAdded=$row['DateAdded'];
      $UserId=$row['UserId'];
      $myTitle = "Recording Metadata for \"$Title\"";

      // Format the output table...
      $o .= "<div class=centered>\n";
      $o .= "   <table class='rv' style='width:98%' rules=groups border=1>\n";
      $o .= "     <tr class=row1>\n";
      $o .= "      <td colspan=2 class=colname>$myTitle</td>\n";
      $o .= "     </tr>\n";
      $o .= "     <tbody><tr class=row0><td></td>\n";
      $o .= "      <td class=datasmall>\n";
      $o .= "        <form name = 'looper' action=''>Auto Loop? &nbsp;&nbsp;<input type='radio' name='autoAdvance' value='$numTracks' onClick='setAutoAdvance(this.value);'>Yes &nbsp;&nbsp; <input type='radio' name='autoAdvance' value='0' checked onClick='setAutoAdvance(0);'>No &nbsp; &nbsp;If there is more than one track the next track will load and start when the current one finishes. </form>\n";
      $o .= "       </td>\n";
      $o .= "     </tr></tbody>\n";
      if ($ShowRecordId == 1) :
          $o .= "     <tr class=row1>\n";
          $o .= "      <td class=fldname width='100'>" . strtoupper("RecordingId") . ":</td>\n";
          $o .= "       <td class=data>$RecordingId </td>\n";
          $o .= "     </tr>\n";
      endif;
      if(strlen($Title) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname width='100'>" . strtoupper("Title") . ":</td>\n";
         $o .= "       <td class=data width='700'>$Title </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Subject) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Subject") . ":</td>\n";
         // This turns each word into a link that fires a keyword search.
         $l = BuildLinks($Subject, " ", "showRecordKeyword");
         $o .= "       <td class=data>$l </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Description) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Description") . ":</td>\n";
         // This turns each word into a link that fires a keyword search.
         $l = BuildLinks($Description, " ", "showRecordKeyword");
         $o .= "       <td class=data>$l </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Creator) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Creator") . ":</td>\n";
         // This turns each word into a link that fires a keyword search.
         $l = BuildLinks($Creator, " ", "showRecordKeyword");
         $o .= "       <td class=data>$l </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Source) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Source") . ":</td>\n";
         $o .= "       <td class=data>$Source </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Publisher) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Publisher") . ":</td>\n";
         $o .= "       <td class=data>$Publisher </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Date) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Date") . ":</td>\n";
         $o .= "       <td class=data>$Date </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($FreeFormDate) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Free Form Date") . ":</td>\n";
         $o .= "       <td class=data>$FreeFormDate </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($PermissionTypeId != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Permission Type") . ":</td>\n";
         $o .= "       <td class=data>" . FetchLookup("Description", "permissiontypes", "PermissionTypeId", $PermissionTypeId) . " </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($AudioFormatId != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Audio Format") . ":</td>\n";
         $o .= "       <td class=data>" . FetchLookup("Extension", "audioformats", "AudioFormatId", $AudioFormatId) . " </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($LangID != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Language") . ":</td>\n";
         $o .= "       <td class=data>" . FetchLookup("LangName", "languages", "LangID", $LangID) . " </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Type) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Type") . ":</td>\n";
         $o .= "       <td class=data>$Type </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Coverage) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Coverage") . ":</td>\n";
         $o .= "       <td class=data>$Coverage </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Spatial) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Location") . ":</td>\n";
         $o .= "       <td class=data>$Spatial </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($Keywords) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Keywords") . ":</td>\n";
         // Keywords are stored as a comma-separated list.
         $l = BuildLinks($Keywords, ",", "showRecordKeyword");
         $o .= "       <td class=data>$l </td>\n";
         $o .= "     </tr>\n";
      endif;
      if(strlen($DateAdded) > 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Date Added") . ":</td>\n";
         $o .= "       <td class=data>$DateAdded </td>\n";
         $o .= "     </tr>\n";
      endif;
      if($UserId != 0) : 
         $o .= "     <tr class=row1>\n";
         $o .= "       <td class=fldname>" . strtoupper("Owner") . ":</td>\n";
         $o .= "       <td class=data>" . FetchLookup("UserEmail", "users", "UserId", $UserId) . " </td>\n";
         $o .= "     </tr>\n";
      endif;
      // The Superuser has UserId = 1, so they can delete the record...
      if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1)) :
          $o .= "     <tr class=row1>\n";
          $o .= "       <td class=fldname style='color:red'>" . strtoupper("Delete Recording") . ":</td>\n";
          $o .= "       <td class=data><a href='#' style='color:red' onClick='delRecord(\"recordView\",\"R=$RecordingId\");'>Click here to Delete.</a> <span style='color:red'>Be careful; deleting the recording also deletes all its tracks, annotations, and transcriptions.</span></td>\n";
          $o .= "     </tr>\n";
      endif;
      // Include the audio tracks at the bottom of the metadata listing...
      $o .= "     <tr class=row1>\n";
      $o .= "       <td colspan=2>\n";
      $o .= FetchTracks($RecordingId, $HotTrack);
      $o .= "       </td>\n";
      $o .= "     </tr>\n";
      $o .= "   </table><br>&nbsp;\n";

      // Include the buttons at the bottom of the listing.
      $o .= "   <a class='b1' href='#' onClick='clearDiv(\"recordView\"); hidePlayer();'>Close Metadata</a> &nbsp; \n";
      // If the user is an Administrator, allow editing and uploading.
      // If not, allow editing their own recordings if they have editing rights.
      // And allow uploading a new recording if they have uploading rights.
      if (($uData['CanModify'] == 1 && $UserId == $myUser) || $uData['IsAdmin'] == 1) :
          $o .= "   <a class='b2' href='#' onClick='showRecordEntry($RecordingId, $CompilationId);'>Edit Metadata</a> &nbsp; \n"; 
      endif;
      if (($uData['CanUpload'] == 1 && $UserId == $myUser) || $uData['IsAdmin'] == 1) :
      //    $o .= "   <a class='b2' href='#' onClick='showRecordEntry(0, $CompilationId);'>Add a New Recording to this Compilation</a> &nbsp; \n"; 
          $o .= "   <a class='b2' href='#' onClick='showTrackEntry(0, $RecordingId);' style='margin-top: 1em; text-decoration:none'>Upload a New Track for this Recording</a> &nbsp; \n";
      endif;
      $o .= "  <div class=hbar></div>\n";
      $o .= "</div>\n";
  endif;
  echo $o;
?>
<script>
  var trackPath = [];
  var trackId = [];
  var details = [];
  var trackStartTime = [];
<?php
   $bq = "SELECT TrackId,FilePath,Identifier,Seconds FROM tracks WHERE RecordingId = '$RecordingId' ORDER BY Identifier";
   $resultall = mysqli_query($conn, $bq);
   $numberall = mysqli_num_rows($resultall);
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
?>
  trackPath.push('<?php echo $myAudioPath; ?>');
  trackId.push(<?php echo $TrackId; ?>);
  trackStartTime.push(<?php echo $startTime; ?>);
  details.push(0);
<?php
          $startTime = $startTime + $Seconds;
          $x++;
  endwhile;
?>
</script>