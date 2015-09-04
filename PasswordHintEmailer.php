<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
  // & ~E_NOTICE
  ini_set("display_errors", 1);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");
  require_once "Mail.php";

  if (isset($_GET['U'])) : 
      $UserEmail = trim($_GET['U']);
      $PWHint = FetchLookup("UserPwReminder", "users", "UserEmail", $UserEmail);
      if (strlen($PWHint) < 1) :
          echo "<h5>No Password Reminder Found!</h5>";
      else :
          $subject = "Your password reminder for the Oral History Toolkit...";
          $message = "Your password reminder is \"$PWHint\".\n\rFor security reasons, you should never use your actual password as a reminder.\n\rUse a phrase or word that will jog your memory, but won't reveal your password to others.\n\r\n\r$emailFrom";

          $headers = array ('From' => $from,'To' => $UserEmail,'Subject' => $subject);
          $smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
          $mail = $smtp->send($UserEmail, $headers, $message);

          if (PEAR::isError($mail)) :
              echo("<p>" . $mail->getMessage() . "</p>");
          else :
              echo("<h5>Your password reminder has been sent to your email account.</h5>");
          endif;
      endif;
  else :
      echo "<h5>No Email Address</h5>";
  endif;
  require_once("login.php");
?>