<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("mp3file.class.php");
  require_once("instanceSettings.php");

  $t = set_time_limit (60);

//---------------------------------------------
function removeslashes($string) {
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}

//---------------------------------------------
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

  $DateMod = date('Y-m-d');
  $response = 0;	// Initialize the response value; a 0 indicates success.

  // Set up the message array, which will be returned based on the value of $response at the end of the script...
  $msg=array();
  $msg[0] = "The upload was successful.";
  $msg[1] = "Upload error 1: The file exceeds the upload_max_filesize directive in php.ini.";
  $msg[2] = "Upload error 2: The file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
  $msg[3] = "Upload error 3: The file was only partially uploaded.";
  $msg[4] = "Upload error 4: No file was uploaded.";
  $msg[5] = "Upload error 5: Invalid file format.  Please select a .mp3 file.";
  $msg[6] = "Upload error 6: The temporary upload folder on the server does not exist.";
  $msg[7] = "Upload error 7: The process failed to write the file to the server's disk.";
  $msg[8] = "Upload error 8: A PHP extension stopped the file upload.";
  $msg[9] = "Upload error 9: Unable to determine the duration of the audio file--it must be VBR or CBR encoded.";
  $msg[10] = "Upload error 10: The file already exists on the server.";

  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :

      // Fetch the variables from the $_POST array and strip any html or php tags...
      $TrackId = strip_tags(mysqli_real_escape_string($conn, $_POST['TrackId']));
      $RecordingId = strip_tags(mysqli_real_escape_string($conn, $_POST['RecordingId']));
      $Identifier = strip_tags(mysqli_real_escape_string($conn, $_POST['Identifier']));
      $Identifier = addslashes($Identifier);
      $Seconds = strip_tags(mysqli_real_escape_string($conn, $_POST['Seconds']));
      $DateAdded = strip_tags(mysqli_real_escape_string($conn, $_POST['DateAdded']));
      $UserId = removeslashes($_POST['UserId']);
      $UserId = trim($UserId,'"');
      $UserId = trim($UserId,"'");

      // Determine whether to update or insert the record, based on the value of the primary key...
      // Check for an existing record based on the positive value of the primary key.
      // If the record exists, fetch it--we are in edit record mode.
      // If the key value is less than 1, then the record does not exist and we are in add mode.

      if ($TrackId > 0) :
          $query="UPDATE tracks SET RecordingId='$RecordingId',Identifier='$Identifier',Seconds='$Seconds',DateAdded='$DateAdded',UserId='$UserId' WHERE TrackId = '$TrackId'";
      else :
          // Process the uploaded file...
          $allowedExts = array("mp3");  // set up as an array in case we add more supported formats later...
          // echo $extension = pathinfo($_FILES['Identifier']['name'], PATHINFO_EXTENSION);
          $fileName = $_FILES['Identifier']['name'];
          $extension = substr($fileName, strrpos($fileName, '.') + 1);
          if(in_array($extension, $allowedExts)) :
             if ($_FILES["Identifier"]["error"] > 0) :
                 $response = $_FILES["Identifier"]["error"];
             else :
                 // All these echo statements are for debugging purposes only, and should be commented out...
                  // echo "Upload: " . $_FILES["Identifier"]["name"] . "<br />";
                  // echo "Type: " . $_FILES["Identifier"]["type"] . "<br />";
                  // echo "Size: " . ($_FILES["Identifier"]["size"] / 1024) . " Kb<br />";
                  // echo "Temp file: " . $_FILES["Identifier"]["tmp_name"] . "<br />";
     
                 if (file_exists($filePath . $_FILES["Identifier"]["name"])) :
                     $response = 10;
                 else :
                     // Calculate duration in seconds...
                     $tmpName = $_FILES["Identifier"]["tmp_name"];
                     $Seconds = fetchMP3Length ("$tmpName");
                     if (is_numeric ($Seconds)) :
                         // Calculate number of transcription blocks based on $DefaultTranscriptionBlockLength...
                         $NumBlocks = intval($Seconds / $DefaultTranscriptionBlockLength);
                         if ($Seconds % $DefaultTranscriptionBlockLength != 0) { $NumBlocks = $NumBlocks + 1 ; }

                         // $filePath is the default path to audio files for this instance of the web app, specified in settings.php.
                         $Identifier = addslashes($_FILES["Identifier"]["name"]);
                         $newName = $filePath . $Identifier;
                         move_uploaded_file($_FILES["Identifier"]["tmp_name"],$newName);
                     else :
                         // It wasn't possible to calculate the duration of the mp3 file, 
                         // because it's not encoded in a format we can use.
                         // So that means we have to abort the upload process.
                         $response = 9;
                     endif;
                 endif;
             endif;
          else :
             // A $response value of 5 indicates an invalid file format.
             $response = 5;
          endif;
          $DateAdded = $DateMod;
          // Insert the MySQL record...
          // The Primary Key value is a sequential number...
          $TrackId = TheNextKeyValue('TrackId','tracks');
          $query="INSERT INTO tracks (TrackId,RecordingId,FilePath,Identifier,Seconds,NumBlocks,DateAdded,UserId) VALUES ('$TrackId','$RecordingId','$filePath','$Identifier','$Seconds', '$NumBlocks','$DateAdded','$UserId')";
      endif;

      // If there are no errors, write the record...
      if ($response == 0) :
          $result = mysqli_query($conn, $query);
          // echo "Query = $query<br>Result = $result<br>\n";
          // Check result
          // This shows the actual query sent to MySQL, and the error. Useful for debugging.
          if (!$result) :
              $message  = 'Invalid query: ' . mysqli_error($conn);
              $message .= 'Whole query: ' . $query;
              die($message);
          endif;
      endif;
  endif;
  // Return the response message, which will be displayed as a javascript alert.
  echo $msg[$response];
?>