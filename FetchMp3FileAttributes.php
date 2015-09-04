<?php
require_once("mp3file.class.php");

function fetchMP3Length ($myFile) {
  $m = new mp3file($myFile);
  $a = $m->get_metadata();
  $myResult = "";
  if ($a['Encoding']=='Unknown')
    $myResult = "?";
  else if ($a['Encoding']=='VBR')
    $myResult = $a['Length'];
  else if ($a['Encoding']=='CBR')
    $myResult = $a['Length'];
  unset($a);
  return $myResult;
}

function updateRecord($myId, $myLength) {
  global $conn;
  $q = "UPDATE recordings SET Seconds ='$myLength' WHERE RecordingId = '$myId'";
  $r = mysqli_query($conn, $q);
//  mysqli_free_result($r);
}
?>