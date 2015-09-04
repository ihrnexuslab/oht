<?php
  //Start session
  ob_start();
  session_start();
  $_SESSION['s_attempts'] = 0;

  // Assign session variables to local variables.
  $myId = $_SESSION['s_user_id'];
  $myEmail = $_SESSION['s_email'];
  $myName =  $_SESSION['s_username'];

//------------------------------------------------
function StripAllSlashes($string) {
  $string = str_replace("\\\"", "\"", $string);
  $string = str_replace("\\'", "'", $string);
  $string = str_replace("\\\\", "\\", $string);
  $string = str_replace("\'", "'", $string);
  return $string;
}
  require_once("settings.php");
  require_once("header-content.php");
?>