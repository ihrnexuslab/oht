<?php
  ob_start();
  session_start();
  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  $o = "&nbsp;<a href='http://gaialab.asu.edu/OHP'>Oral History Project Homepage</a>";
  if (!is_null($uData['UserName'])) :
      $o .= " -> You are logged in as " . $uData['UserName'] . ". -> <a href='#' onClick='processAjax(\"logout.php\", \"logIn\");'>Log Out</a>";
  endif;
  echo $o;
  $o = "";
?>