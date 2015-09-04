<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  // & ~E_NOTICE
  ini_set("display_errors", 1);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

/*
  // For testing purposes, remove comments on the block of code that sets up the header.
  $h  = "<!DOCTYPE html>\n";
  $h .= "<html lang='en'>\n";
  $h .= "  <head>\n";
  $h .= "    <meta charset='UTF-8'>\n";
  $h .= "    <link rel='stylesheet' href='css/default.css'>\n";
  $h .= "    <script src='javaScripts/OHP.js' language='javascript' type='text/javascript'></script>\n";
  $h .= "    <script src='javaScripts/validate.js' language='javascript' type='text/javascript'></script>\n";
  $h .= "    <script src='javaScripts/default.js' language='javascript' type='text/javascript'></script>\n";
  $h .= "    <title>The ASU Social Scribe Project</title>\n";
  $h .= "  </head>\n";
  $h .= "  <body>\n";
  echo $h;
*/

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }
  $DateMod = date('Y-m-d');

  if (isset($_GET['TId'])) { $TrackId=$_GET['TId']; }
  if (isset($_GET['RId'])) { $RecordingId = $_GET['RId']; }

  if ($TrackId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM tracks WHERE TrackId = '$TrackId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $TrackId = $fields["TrackId"];
      $RecordingId = $fields["RecordingId"];
      $Identifier = $fields["Identifier"];
      $Seconds = $fields["Seconds"];
      $NumBlocks = $fields["NumBlocks"];
      $DateAdded = $fields["DateAdded"];
      $UserId = $fields["UserId"];
      $Heading = "Edit an Existing Track:\n";
      $Title .= "Edit the form and press 'Submit'......";
  else :
      // This is a new record, which has not yet been submitted...
      $MyRecording = FetchLookup("Title", "recordings", "RecordingId", $RecordingId);
      $Heading = "Upload a New Track for $MyRecording: \n";
      $Title = "Fill in the form and press 'Submit'...\n";
  endif;

  $o = "";
  $o .= "<div class=centered>\n";
  $o .= "  <h4>$Heading</h4>\n";
  $o .= "  <!-- The data encoding type, enctype, MUST be specified as below -->\n";
  $o .= "  <form name=formTrk id=formTrk enctype='multipart/form-data' action='WriteTrackMetadata.php' method=post target='resIFrame'>\n";
  $o .= "    <!-- MAX_FILE_SIZE must precede the file input  -->\n";
  $o .= "    <input type='hidden' name='MAX_FILE_SIZE' value='10000000'>\n";
  $o .= "    <input type='hidden' name='TrackId' value=\'$TrackId\'>\n";
  $o .= "    <input type='hidden' name='RecordingId' value='$RecordingId'>\n";
  $o .= "    <input type='hidden' name='UserId' value=\'$myUser\'>\n";
  $o .= "    <input type='hidden' name='Action' value='Submit'>\n";
  $o .= "    <table class='rv' rules=rows style='width: 820px'>\n";
  $o .= "      <thead><tr class=tblname>\n";
  $o .= "        <td colspan=2>$Title</td>\n";
  $o .= "      </tr></thead>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Track File<span style='color:red'>*</span>:</td>\n";
  $o .= "          <td colspan=3>\n";
  if ($TrackId > 0) :
      $o .= "            <input size=110 type='text' name='Identifier' id='Identifier' value='$Identifier' readonly >\n";
  else :
      $o .= "            <input size=110 type='file' name='Identifier' id='Identifier' value='$Identifier' required>\n";
  endif;
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tfoot><tr class=footer>\n";
  $o .= "        <td colspan=4 class=colname style='text-align: left'>\n";
  $o .= "            <input type='button' class='b2' id='Submit' value='Submit' onclick='document.formTrk.submit()'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.formTrk.reset()'>&nbsp;&nbsp;\n";
  if ($TrackId > 0) :
      $o .= "            <input type='button' class='b2' onclick='showRecordView($RecordingId, $TrackId);' value='Cancel'>&nbsp;&nbsp;Fields with an asterisk(*) are required.\n";
  else :
      $o .= "            <input type='button' class='b2' onclick='showRecordView($RecordingId);' value='Cancel'>&nbsp;&nbsp;Fields with an asterisk(*) are required.\n";
  endif;
  $o .= "          <iframe id='resIFrame' name='resIFrame' src='' onLoad='uploadDone(this.name,$RecordingId)' style='width:0; height:0; border:none'></iframe>\n";
  $o .= "        </td>\n";
  $o .= "      </tr></tfoot>\n";
  $o .= "    </table>\n";
  $o .= "  </form>\n";
  $o .= "</div>\n";
  echo $o;
?>