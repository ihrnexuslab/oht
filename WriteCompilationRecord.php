<?php
  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $ModBy = $myUser;
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
  $h .= "    <title>The ASU Social Scribe Project</title>\n";
  $h .= "  </head>\n";
  $h .= "  <body>\n";
  echo $h;
*/
  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :
      // Fetch the variables from the $_POST array and strip any html or php tags...
      $CompilationId = mysqli_real_escape_string($conn, $_POST['CompilationId']);
      $Description = strip_tags(mysqli_real_escape_string($conn, $_POST['Description']));
      $DateAdded = strip_tags(mysqli_real_escape_string($conn, $_POST['DateAdded']));
      $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
      $mySet = strip_tags(mysqli_real_escape_string($conn, $_POST['mySet']));
      // Determine whether to update or insert the record, based on the value of the primary key...
      // Check for an existing record based on the positive value of the primary key.
      // If the record exists, fetch it--we are in edit record mode.
      // If the key value is less than 1, then the record does not exist and we are in add mode.
      if ($CompilationId > 0) :
          $query="UPDATE compilations SET Description='$Description' WHERE CompilationId = '$CompilationId'";
          $newRec=0;
      else :
          // Insert the MySQL record...
          // The Primary Key value is a sequential number...
          $CompilationId = TheNextKeyValue('CompilationId','compilations');
          $Creator = FetchLookup("UserName", "users", "UserId", $UserId);
          $query="INSERT INTO compilations (CompilationId,PrefixCode,Description,Creator,DateAdded,UserId) VALUES ('$CompilationId','$DefaultPrefixCode','$Description','$Creator','$DateMod','$UserId')";
          $newRec = 1;
      endif;
      $result = mysqli_query($conn, $query);
      // Check result
      // This shows the actual query sent to MySQL, and the error. Useful for debugging.
      if (!$result) :
          $message  = 'Invalid query: ' . mysqli_error($conn);
          $message .= 'Whole query: ' . $query;
          die($message);
      else :
          if ($newRec == 1) :
              // Create a record in the compilations2users table for the user, as the admin
              // The Primary Key value is a sequential number...
              $Compilation2UserId = TheNextKeyValue('Compilation2UserId','compilation2user');
              $query="INSERT INTO compilation2user (Compilation2UserId,CompilationId,UserId,IsAdmin,CanUpload,CanAnnotate,CanDownload,CanAdd,CanModify,EnteredBy,DateEntered) VALUES ('$Compilation2UserId','$CompilationId','$UserId','$CompUserIsAdmin','$CompUserCanUpload','$CompUserCanAnnotate','$CompUserCanDownload','$CompUserCanAdd','$CompUserCanModify','$Creator','$DateMod')";

              // These are the suggested default values for new compilations for the user:
              // $CompUserIsAdmin = 1;
              // $CompUserCanUpload = 1;
              // $CompUserCanAnnotate = 1;
              // $CompUserCanDownload = 0;
              // $CompUserCanAdd = 1;
              // $CompUserCanModify = 1;

              $result = mysqli_query($conn, $query);
              // Check result
              // This shows the actual query sent to MySQL, and the error. Useful for debugging.
              if (!$result) :
                  $message  = 'Invalid query: ' . mysqli_error($conn);
                  $message .= 'Whole query: ' . $query;
                  die($message);
              endif;
          endif;
          // echo "mySet = $mySet<br>\n";
          unset($CompilationId);
          if ($UseCompilationsUsers == 1) :
              require_once("UserCompilationsLister.php");
          else :
              require_once("CompilationsLister.php");
          endif;
      endif;
  endif;
?>