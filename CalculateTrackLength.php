<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  set_time_limit(120);

  require_once("OralHistoryDataConn.php");
  require_once("mp3file.class.php");
  require_once("settings.php");

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

  $o = "<h2>Calculated Length of Audio Tracks:</h2>\n";

  $bq = "Select TrackId, Identifier FROM Tracks ORDER BY TrackId";
  $bqresult = mysqli_query($conn, $bq);
  $bqnumber = mysqli_num_rows($bqresult);
  if ($bqnumber > 0) :  
      $x=0;
      while ($x<$bqnumber):
             // Retreive Data and put it in Local Variables for each Row...
             $row = mysqli_fetch_array($bqresult, MYSQLI_ASSOC);
             $TrackId = $row['TrackId'];
             $Identifier = $row['Identifier'];
             // Now figure out how long it is in seconds...
             $Seconds = fetchMP3Length ("$Identifier");
             $NumBlocks = intval($Seconds / 5);
             if ($Seconds % 5 != 0) { $NumBlocks = $NumBlocks + 1 ; }

             $query="UPDATE tracks SET Seconds='$Seconds', NumBlocks='$NumBlocks' WHERE TrackId = '$TrackId'";
             $result = mysqli_query($conn, $query);
             // Check result
             // This shows the actual query sent to MySQL, and the error. Useful for debugging.
             if (!$result) :
                 $message  = 'Invalid query: ' . mysqli_error($conn);
                 $message .= 'Whole query: ' . $query;
                 die($message);
             else :
                 $o .= "$Identifier length = $Seconds, Number of blocks = $NumBlocks<br>\n";
             endif;
             $x++;
      endwhile;
  endif;
  echo $o;
?>