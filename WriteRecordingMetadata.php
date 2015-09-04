<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("settings.php");
  require_once("LookupFunctions.php");
  require_once("MailFunctions.php");

  $DateMod = date('Y-m-d');
  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :

      // Fetch the variables from the $_POST array and strip any html or php tags...
      $RecordingId = strip_tags(mysqli_real_escape_string($conn, $_POST['RecordingId']));
      $CompilationId = strip_tags(mysqli_real_escape_string($conn, $_POST['CompilationId']));
      $Title = strip_tags(mysqli_real_escape_string($conn, $_POST['Title']));
      $Subject = strip_tags(mysqli_real_escape_string($conn, $_POST['Subject']));
      $Description = strip_tags(mysqli_real_escape_string($conn, $_POST['Description']));
      $Creator = strip_tags(mysqli_real_escape_string($conn, $_POST['Creator']));
      $Source = strip_tags(mysqli_real_escape_string($conn, $_POST['Source']));
      $Publisher = strip_tags(mysqli_real_escape_string($conn, $_POST['Publisher']));
      $Date = strip_tags(mysqli_real_escape_string($conn, $_POST['Date']));
      $FreeFormDate = strip_tags(mysqli_real_escape_string($conn, $_POST['FreeFormDate']));
      $PermissionTypeId = strip_tags(mysqli_real_escape_string($conn, $_POST['PermissionTypeId']));
      $AudioFormatId = strip_tags(mysqli_real_escape_string($conn, $_POST['AudioFormatId']));
      $LangID = strip_tags(mysqli_real_escape_string($conn, $_POST['LangID']));
      $Type = strip_tags(mysqli_real_escape_string($conn, $_POST['Type']));
      $Coverage = strip_tags(mysqli_real_escape_string($conn, $_POST['Coverage']));
      $Spatial = strip_tags(mysqli_real_escape_string($conn, $_POST['Spatial']));
      $Keywords = strip_tags(mysqli_real_escape_string($conn, $_POST['Keywords']));
      $Keywords = trim($Keywords);
      $DateAdded = strip_tags(mysqli_real_escape_string($conn, $_POST['DateAdded']));
      $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
      $Publish = strip_tags(mysqli_real_escape_string($conn, $_POST['Publish']));

      // Fetch the user name
      $UserName = FetchLookup("UserName", "users", "UserId", $UserId);

      // Determine whether to update or insert the record, based on the value of the primary key...
      // Check for an existing record based on the positive value of the primary key.
      // If the record exists, fetch it--we are in edit record mode.
      // If the key value is less than 1, then the record does not exist and we are in add mode.

      if ($RecordingId > 0) :
          $query="UPDATE recordings SET Title='$Title',Subject='$Subject',Description='$Description',Creator='$Creator',Source='$Source',Publisher='$Publisher',`Date`='$Date',FreeFormDate='$FreeFormDate',PermissionTypeId='$PermissionTypeId',AudioFormatId='$AudioFormatId',LangID='$LangID',Type='$Type',Coverage='$Coverage',Seconds='$Seconds',`Spatial`='$Spatial',Keywords='$Keywords',DateAdded='$DateAdded',UserId='$UserId',Publish='$Publish' WHERE RecordingId = '$RecordingId'";

          $Heading = "Updated Recording Metadata.";
      else :
          // Insert the MySQL record...
          // The Primary Key value is a sequential number...
          $RecordingId = TheNextKeyValue('RecordingId','recordings');
          $DateAdded = $DateMod;
          $query="INSERT INTO recordings (RecordingId,CompilationId,PrefixCode,Title,Subject,Description,Creator,Source,Publisher,`Date`,FreeFormDate,PermissionTypeId,AudioFormatId,LangID,Type,Coverage,`Spatial`,Keywords,DateAdded,UserId,Publish) VALUES ('$RecordingId','$CompilationId','$DefaultPrefixCode','$Title','$Subject','$Description','$Creator','$Source','$Publisher','$Date','$FreeFormDate','$PermissionTypeId','$AudioFormatId','$LangID','$Type','$Coverage','$Spatial','$Keywords','$DateAdded','$UserId','$Publish')";
          $Heading = "Added Recording Metadata.";

          // Send an e-mail message to the system moderator.


      endif;
      $result = mysqli_query($conn, $query);
      // Check result
      // This shows the actual query sent to MySQL, and the error. Useful for debugging.
      if (!$result) :
          $message  = 'Invalid query: ' . mysqli_error($conn);
          $message .= 'Whole query: ' . $query;
          die($message);
      else :
          // Send an e-mail to the instance moderator, notifying of new recording...
          if ($SendEmailOnNewRecording == 1 && $Heading == "Added Recording Metadata.") :
              $from = "admin@oralhistorytools.org>";
              $to = $Moderator;
              $subject = "New Recording created...";
              $message = "$UserName added a new recording, $Title, on $DateMod. Use <a href='oralhistorytools.org/OHP/index.php?RId=$RecordingId'>oralhistorytools.org/OHP/index.php?RId=$RecordingId</a> to link directly to the record.";
              $m = sendEMail($to, $from, $subject, $message);
          endif;
      endif;
  endif;
  // The record has been written, now display the record view...
  // echo "Query = $query<br>Result = $result<br>\n";
  require_once("RecordingsRecordView.php");
?>