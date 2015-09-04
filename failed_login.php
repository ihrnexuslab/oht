<?php
  // Log the user out and destroy the session information...
  session_start();
  setcookie(session_name(), '', 100);
  session_unset();
  session_destroy();
  $_SESSION = array();
  require_once("header-content.php");
  $o = "  <div class=center>\n";
  $o .= "   <h1>HPS Repository Admin Login Failure</h1>\n";
  $o .= "   <p>Your login attempt has failed three times. You shall not pass.</p>\n";
  $o .= " </div>\n";
  echo $o;
  $o = "";
  require_once("footer.php");
?>