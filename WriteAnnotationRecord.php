<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("settings.php");

  // Fetch the variables from the $_POST array and strip any html or php tags...
  $AnnotationId = mysqli_real_escape_string($conn, $_POST['AnnotationId']);
  $TrackId = mysqli_real_escape_string($conn, $_POST['TrackId']);
  $AnnotationTypeId = mysqli_real_escape_string($conn, $_POST['AnnotationTypeId']);
  $SecondsIn = mysqli_real_escape_string($conn, $_POST['SecondsIn']);
  $SecondsOut = mysqli_real_escape_string($conn, $_POST['SecondsOut']);
  $Description = mysqli_real_escape_string($conn, $_POST['Description']);
  $Keywords = mysqli_real_escape_string($conn, $_POST['Keywords']);
  $Location = mysqli_real_escape_string($conn, $_POST['Location']);
  $Latitude = mysqli_real_escape_string($conn, $_POST['Latitude']);
  if ($Latitude == "") { $Latitude=NULL;}
  $Longitude = mysqli_real_escape_string($conn, $_POST['Longitude']);
  if ($Longitude == "") { $Longitude=NULL;}
  $UserId = mysqli_real_escape_string($conn, $_POST['UserId']);
  $Action = mysqli_real_escape_string($conn, $_POST['Action']);

  // Determine whether to update or insert the record, based on the value of the primary key...
  // Check for an existing record based on the positive value of the primary key.
  // If the record exists, fetch it--we are in edit record mode.
  // If the key value is less than 1, then the record does not exist and we are in add mode.

  if ($AnnotationId > 0) :
      // The record already exists, so create an UPDATE query...
      $query="UPDATE annotation SET AnnotationId='$AnnotationId',TrackId='$TrackId',AnnotationTypeId='$AnnotationTypeId',SecondsIn='$SecondsIn',SecondsOut='$SecondsOut',Description='$Description',Keywords='$Keywords',Location='$Location',Latitude='$Latitude',Longitude='$Longitude',UserId='$UserId' WHERE AnnotationId = '$AnnotationId'";
  else :
      // The record does not exist, so create an INSERT query...
      // The Primary Key value is a sequential number...
      $AnnotationId = TheNextKeyValue('AnnotationId','annotation');
      $DateAdded = date('Y-m-d');
      $query="INSERT INTO annotation (AnnotationId,TrackId,AnnotationTypeId,SecondsIn,SecondsOut,Description,Keywords,Location,Latitude,Longitude,DateAdded,UserId) VALUES ('$AnnotationId','$TrackId','$AnnotationTypeId','$SecondsIn','$SecondsOut','$Description','$Keywords','$Location','$Latitude','$Longitude','$DateAdded','$UserId')";
  endif;
  //echo "Query=$query<br>\n";
  $result = mysqli_query($conn, $query);
  // Check result
  // This shows the actual query sent to MySQL, and the error. Useful for debugging.
  if (!$result) :
      $message  = 'Invalid query: ' . mysqli_error($conn);
      $message .= 'Whole query: ' . $query;
      die($message);
  else : 
      // The record was handled properly, now reload the list of annotations.
      require_once("AnnotationLister.php");
  endif;
?>