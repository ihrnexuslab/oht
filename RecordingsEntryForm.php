<?php

  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  // & ~E_NOTICE
  ini_set("display_errors", 1);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }

  $DateMod = date('Y-m-d');
  // For testing purposes, remove comments on the block of code that sets up the header.
/*
  $h  = "<!DOCTYPE html>\n";
  $h .= "<html lang='en'>\n";
  $h .= "  <head>\n";
  $h .= "    <meta charset='UTF-8'>\n";
  $h .= "    <link rel='stylesheet' href='css/default.css'>\n";
  $h .= "    <script src='javaScripts/validate.js' language='javascript' type='text/javascript'></script>\n";
  $h .= "    <script src='javaScripts/default.js' language='javascript' type='text/javascript'></script>\n";
  $h .= "    <title>$PageTitle</title>\n";
  $h .= "  </head>\n";
  $h .= "  <body>\n";
  echo $h;
*/
  $RecordingId=$_GET['RId'];
  $CompilationId=$_GET['CId'];
  if ($DefaultCompilationId > 0) { $CompilationId = $DefaultCompilationId; }

  if ($RecordingId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM recordings WHERE RecordingId = '$RecordingId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $RecordingId = $fields["RecordingId"];
      $CompilationId = $fields["CompilationId"];
      $Title = $fields["Title"];
      $Subject = $fields["Subject"];
      $Description = $fields["Description"];
      $Creator = $fields["Creator"];
      $Source = $fields["Source"];
      $Publisher = $fields["Publisher"];
      $Date = $fields["Date"];
      $FreeFormDate = $fields["FreeFormDate"];
      $PermissionTypeId = $fields["PermissionTypeId"];
      $AudioFormatId = $fields["AudioFormatId"];
      $LangID = $fields["LangID"];
      $Type = $fields["Type"];
      $Coverage = $fields["Coverage"];
      $Seconds = $fields["Seconds"];
      $Spatial = $fields["Spatial"];
      $Keywords = $fields["Keywords"];
      $DateAdded = $fields["DateAdded"];
      $UserId = $fields["UserId"];
      $Publish = $fields["Publish"];
      $Heading = "The Metadata Record Already Exists.\n";
      $myTitle = "Edit Metadata for $Title...";
  else :
      // This is a new record, which has not yet been submitted...
      $Heading = "Enter a New Recording Metadata Record!\n";
      $myTitle = "Fill in the form to create a new recording. then press 'Submit'...\n";
      $UserId = $myUser;
      $Publish = $PublishByDefault;
  endif;

  // This is the data entry form...
  $o = "";
  $o .= "  <form name=formRec method=post>\n";
  $o .= "    <input type='hidden' name='RecordingId' value='$RecordingId'>\n";
  $o .= "    <input type='hidden' name='CompilationId' value='$CompilationId'>\n";
  $o .= "    <input type='hidden' name='DateAdded' value='$DateAdded'>\n";
  $o .= "    <input type='hidden' name='Action' value='Submit'>\n";
  $o .= "    <table class='rv' rules=rows style='width: 800px; font-size: 8pt;'>\n";
  $o .= "      <thead><tr class=tblname>\n";
  $o .= "        <td colspan=4 class=colname>$myTitle</td>\n";
  $o .= "      </tr></thead>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Title<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Title' id='Title' value='$Title' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Subject<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Subject' id='Subject' value='$Subject' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Description<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <textarea name='Description' wrap='SOFT' rows=3 style='width: 98%;' >$Description</textarea>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td style='width: 150px' class=caption>Creator<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=80 type='text' name='Creator' id='Creator' value='$Creator' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  if ($uData['IsAdmin'] == 1) :
      $o .= "          <td style='width: 150px' class=caption>Owner<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td colspan=3>\n";
      $o .=              MakeList ("UserId", "users", "UserId", "UserEmail", "PrefixCode = '$DefaultPrefixCode'", "UserEmail", $myUser, false);
      $o .= "          </td>\n";
  else :
      $o .= "          <td colspan=4>\n";
      $o .= "            <input type='hidden' name='UserId' value='$UserId'>\n";
      $o .= "          </td>\n";
  endif;
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Source<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <textarea name='Source' wrap='SOFT' rows=3 style='width: 98%;' >$Source</textarea>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Publisher :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Publisher' id='Publisher' value='$Publisher' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Date :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <INPUT TYPE='text' NAME='Date' VALUE='$Date' > <span id='testdiv1'><A HREF='#' onClick=\"cal19.select(document.forms['formRec'].Date,'anchor1','yyyy-MM-dd'); return false;\" NAME='anchor1' ID='anchor1'>Select date</A></span>\n";
  $o .= "          </td>\n";
  $o .= "          <td class=caption>Free Form Date :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input size=17 type='text' name='FreeFormDate' id='FreeFormDate' value='$FreeFormDate' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Permission :</td>\n";
  $o .= "          <td>\n";
  $o .=              MakeLookup('PermissionTypeId','permissiontypes','PermissionTypeId','PermissionType','List','PermissionType',$PermissionTypeId);
  $o .= "          </td>\n";
  $o .= "          <td class=caption style='width: 150px'>Audio Format :</td>\n";
  $o .= "          <td>\n";
  $o .=              MakeLookup('AudioFormatId','audioformats','AudioFormatId','Extension','List','Extension',$AudioFormatId);
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Language :</td>\n";
  $o .= "          <td>\n";
  $o .=              MakeLookup('LangID','languages','LangID','LangName','List','LangName',$LangID);
  $o .= "          </td>\n";
  $o .= "          <td class=caption style='width: 150px'>Type :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input size=17 type='text' name='Type' id='Type' value='Sound' readonly >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Temporal Coverage :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Coverage' id='Coverage' value='$Coverage' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Spatial Coverage :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Spatial' id='Spatial' value='$Spatial' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width: 150px'>Keywords :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input size=100 type='text' name='Keywords' id='Keywords value='$Keywords' ><p class=bib>Comma separated list e.g. keyword1, keyword2, keyword3</p>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption style='width: 150px'>Published? :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <select name='Publish'>\n";
  if ($Publish == 1) :
      $o .= "              <option value='1' selected>Yes</option>\n";
      $o .= "              <option value='0'>No</option>\n";
  else :
      $o .= "              <option value='1'>Yes</option>\n";
      $o .= "              <option value='0' selected>No</option>\n";
  endif;
  $o .= "            </select>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tfoot><tr class=footer>\n";
  $o .= "        <td colspan=4 class=colname style='text-align: left'>\n";
  $o .= "            <input type='button' class='b2' id='Submit' onClick='verifyRecording(this.form);' value='Submit'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.formRec.reset()'>&nbsp;&nbsp;\n";
  if ($RecordingId > 0) :
      $o .= "            <input type='button' class='b2' onclick='showRecordView($RecordingId, \"recordView\");' value='Cancel'>&nbsp;&nbsp;Fields with an asterisk(<span style='color:red'>*</span>) are required.</td>\n";
  else :
      $o .= "            <input type='button' class='b2' onclick='clearDiv(\"recordView\");' value='Cancel'>&nbsp;&nbsp;Fields with an asterisk(<span style='color:red'>*</span>) are required.</td>\n";
  endif;
  $o .= "      </tr></tfoot>\n";
  $o .= "    </table>\n";
  $o .= "  </form>\n";
  $o .= "  <hr style='color:#1F3F5E;background-color:#1F3F5E;height:1px;border:none;'>\n";
//  if ($RecordingId>0):
//      $o .= "<br><a href='#' class=b2 style='width: 100px; text-decoration: none;' onClick='showRecordEntry(0);'>Add Another</a>&nbsp;&nbsp;";
//  endif;
//  $o .= "</div>\n";
  echo $o;
  $o = "";
//  $f = "  </body>\n";
//  $f .= "</html>\n";
//echo $f;
//$f = "";
?>