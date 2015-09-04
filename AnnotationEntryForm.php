<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $o = "";

  // The form was not submitted; get the primary key value...
  if (!is_null($_GET['AnnotationId'])) { $AnnotationId=$_GET['AnnotationId'];}
  if (!is_null($_GET['TS'])) { $SecondsIn=round($_GET['TS'], 2);}
  if (!is_null($_GET['TId'])) :
      $TrackId=$_GET['TId'];
      $Seconds = FetchLookup("Seconds", "tracks", "TrackId", $TrackId);
  endif;

  if (!is_null($_GET['TSOut'])) { $SecondsOut=$_GET['TSOut'];}

  if ($AnnotationId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM annotation WHERE AnnotationId = '$AnnotationId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $AnnotationId = $fields["AnnotationId"];
      $TrackId = $fields["TrackId"];
      $AnnotationTypeId = $fields["AnnotationTypeId"];
      $SecondsIn = $fields["SecondsIn"];
      $SecondsOut = $fields["SecondsOut"];
      $Description = $fields["Description"];
      $Keywords = $fields["Keywords"];
      $Location = $fields["Location"];
      $Latitude = $fields["Latitude"];
      $Longitude = $fields["Longitude"];
      $DateAdded = $fields["DateAdded"];
      $UserId = $fields["UserId"];
      $Title .= "Edit the Annotation Record...";
  else :
      // This is a new record, which has not yet been submitted...
      $Title = "Enter the Annotation Metadata; Fill in the form and press 'Submit'...\n";
      // For new annotations, set the end point equal to the end of the audio track...
      $SecondsOut = $Seconds;
  endif;

  $o .= "<div class=centered>\n";
  $o .= "  <form name=formAnn id=formAnn method=post>\n";
  $o .= "    <input type='hidden' name='AnnotationId' value='$AnnotationId'>\n";
  $o .= "    <input type='hidden' name='TrackId' value='$TrackId'>\n";
  $o .= "    <input type='hidden' name='TranscriptId' value='$TranscriptId'>\n";
  $o .= "    <input type='hidden' name='SecondsIn' value='$SecondsIn'>\n";
  $o .= "    <input type='hidden' name='SecondsOut' value='$SecondsOut'>\n";
  $o .= "    <input type='hidden' name='DateAdded' value='$DateAdded'>\n";
  $o .= "    <input type='hidden' name='UserId' value='$UserId'>\n";
  $o .= "    <input type='hidden' name='Action' value=''>\n";
  $o .= "    <table rules=rows >\n";
  $o .= "      <thead><tr class=colname>\n";
  $o .= "        <td colspan=4>$Title</td>\n";
  $o .= "      </tr></thead>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption>Annotation Type<span style='color:red'>*</span>:</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .=              MakeLookup('AnnotationTypeId','annotationtype','AnnotationTypeId','AnnotationType','List','AnnotationType',$AnnotationTypeId);
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Description<span style='color:red'>*</span>:</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input type='text' name='Description' id='Description' value='$Description' style='width:420px'>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption>Keywords<br>(Comma separated list):</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input type='text' name='Keywords' id='Keywords' value='$Keywords' style='width:420px'>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
/*
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Location :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input type='text' name='Location' id='Location' value='$Location' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption>Latitude :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input type='text' name='Latitude' id='Latitude' value='$Latitude' oninput='checkFloatRange(this.value, -90.0, 90.0)'>\n";
  $o .= "          </td>\n";
  $o .= "          <td class=caption>Longitude :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input type='text' name='Longitude' id='Longitude' value='$Longitude' oninput='checkFloatRange(this.value, -180.0, 180.0)'>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
*/
  $o .= "      <tfoot>\n";
  $o .= "        <tr class=colname>\n";
  $o .= "          <td colspan=4 style='vertical-align:middle'>\n";
  $o .= "            <input type='button' class='b2' id='Submit' value='Submit' onClick='verifyAnnotation(\"formAnn\");'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.formAnn.reset()'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Cancel' value='Cancel'onclick='loadAnnotations($TrackId); wavesurfer.clearRegions();'>&nbsp;&nbsp;\n";
  $o .= "            <span class=bib>Fields with an asterisk(<span style='color:red'>*</span>) are required.</span></td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tfoot>\n";
  $o .= "    </table>\n";
  $o .= "  </form>\n";
  $o .= "</div>\n";
  echo   $o;
?>