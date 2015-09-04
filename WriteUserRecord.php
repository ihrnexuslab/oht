<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  //Start session
  session_start();
  $ModBy = $_SESSION['s_user_id'];
  $DateMod = date('Y-m-d');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("settings.php");

  require_once('recaptchalib.php');
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) :
      // What happens when the CAPTCHA was entered incorrectly
      die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
           "(reCAPTCHA said: " . $resp->error . ")");
  else :
    // Your code here to handle a successful verification

    // Check to see if the form was submitted, and if it was, write the record...
    if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :
      // Fetch the variables from the $_POST array and strip any html or php tags...
      $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
      $UserName = strip_tags(mysqli_real_escape_string($conn, $_POST['UserName']));
      $UserAddress = strip_tags(mysqli_real_escape_string($conn, $_POST['UserAddress']));
      $UserPhone = strip_tags(mysqli_real_escape_string($conn, $_POST['UserPhone']));
      $UserMobilePhone = strip_tags(mysqli_real_escape_string($conn, $_POST['UserMobilePhone']));
      $UserFax = strip_tags(mysqli_real_escape_string($conn, $_POST['UserFax']));
      $UserEmail = strip_tags(mysqli_real_escape_string($conn, $_POST['UserEmail']));
      $UserURL = strip_tags(mysqli_real_escape_string($conn, $_POST['UserURL']));
      $UserPw = strip_tags(mysqli_real_escape_string($conn, $_POST['UserPw'])); 
      $UserPwReminder = strip_tags(mysqli_real_escape_string($conn, $_POST['UserPwReminder']));
      $CountryID = strip_tags(mysqli_real_escape_string($conn, $_POST['CountryID']));
      $IsAdmin = strip_tags(mysqli_real_escape_string($conn, $_POST['IsAdmin']));
      $CanUpload = strip_tags(mysqli_real_escape_string($conn, $_POST['CanUpload']));
      $AnnotatesOwn = strip_tags(mysqli_real_escape_string($conn, $_POST['AnnotatesOwn']));
      $AnnotatesAll = strip_tags(mysqli_real_escape_string($conn, $_POST['AnnotatesAll']));
      $CanDownload = strip_tags(mysqli_real_escape_string($conn, $_POST['CanDownload']));
      $CanAdd = strip_tags(mysqli_real_escape_string($conn, $_POST['CanAdd']));
      $CanModify = strip_tags(mysqli_real_escape_string($conn, $_POST['CanModify']));
      $EnteredBy = $ModBy;
      $DateEntered = strip_tags(mysqli_real_escape_string($conn, $_POST['DateEntered']));

      // Determine whether to update or insert the record, based on the value of the primary key...
      // Check for an existing record based on the positive value of the primary key.
      // If the record exists, fetch it--we are in edit record mode.
      // If the key value is less than 1, then the record does not exist and we are in add mode.

      if ($UserId > 0) :
          $query="UPDATE users SET UserId='$UserId',UserName='$UserName',UserAddress='$UserAddress',UserPhone='$UserPhone',UserMobilePhone='$UserMobilePhone',UserFax='$UserFax',UserEmail='$UserEmail',UserURL='$UserURL',CountryID='$CountryID',IsAdmin='$IsAdmin',CanUpload='$CanUpload',AnnotatesOwn='$AnnotatesOwn',AnnotatesAll='$AnnotatesAll',CanDownload='$CanDownload',CanAdd='$CanAdd',CanModify='$CanModify',EnteredBy='$EnteredBy',DateEntered='$DateEntered' WHERE UserId = '$UserId'";
      else :
          // Insert the MySQL record...
          // The Primary Key value is a sequential number...
          $UserId = TheNextKeyValue('UserId','users');
          $Salt = "Oral History Project 2014";
          $hash = sha1($salt.$UserPw);
          $query="INSERT INTO users (UserId,PrefixCode,UserName,UserAddress,UserPhone,UserMobilePhone,UserFax,UserEmail,UserURL,UserPw,Salt,UserPwReminder,CountryID,IsAdmin,CanUpload,AnnotatesOwn,AnnotatesAll,CanDownload,CanAdd,CanModify,EnteredBy,DateEntered) VALUES ('$UserId','$DefaultPrefixCode','$UserName','$UserAddress','$UserPhone','$UserMobilePhone','$UserFax','$UserEmail','$UserURL','$hash','$Salt','$UserPwReminder','$CountryID','$IsAdmin','$CanUpload','$AnnotatesOwn','$AnnotatesAll','$CanDownload','$CanAdd','$CanModify','$EnteredBy','$DateEntered')";
      endif;
      $result = mysqli_query($conn, $query);

      // Check result
      // This shows the actual query sent to MySQL, and the error. Useful for debugging.
      if (!$result) :
          $message  = 'Invalid query: ' . mysqli_error($conn);
          $message .= 'Whole query: ' . $query;
          die($message);
      endif;
    endif;
    // The record has been written, now display the record view...
    // echo "Query = $query<br>Result = $result<br>\n";
    require_once("UsersRecordView.php");
  endif;
?>