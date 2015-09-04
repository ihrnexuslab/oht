<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  //Start session
  session_start();
  $ModBy = $_SESSION['s_user_id'];
  $DateMod = date('Y-m-d');

  require_once("OralHistoryDataConn.php");
  require_once("settings.php");

  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :
      // Fetch the variables from the $_POST array and strip any html or php tags...
      $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
      $UserPw = strip_tags(mysqli_real_escape_string($conn, $_POST['UserPw']));
      $UserPwReminder = strip_tags(mysqli_real_escape_string($conn, $_POST['UserPwReminder']));
      // print_r($_POST);
      if ($UserId > 0) :
          $Salt = "Oral History Project 2014";
          $hash = sha1($salt.$UserPw);
          $query="UPDATE users SET UserPw='$hash',Salt='$Salt',UserPwReminder='$UserPwReminder' WHERE UserId = '$UserId'";
          $result = mysqli_query($conn, $query);
          // Check result
          // This shows the actual query sent to MySQL, and the error. Useful for debugging.
          if (!$result) :
              $message  = 'Invalid query: ' . mysqli_error($conn);
              $message .= 'Whole query: ' . $query;
              die($message);
          endif;
          echo "<h4>Password Reset!</h4>";
      endif;
  else :
      echo "<h4>No User Record.<br>Password Not Reset!</h4>";
  endif;
?>