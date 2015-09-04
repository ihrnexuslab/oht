<?php
  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $where = "WHERE PrefixCode = '$DefaultPrefixCode' ";

  if (isset($_POST['UserId'])) { $UserId = $_POST['UserId']; }
  if (isset($_GET['UserId'])) { $UserId = $_GET['UserId']; }

  if ($sortby!=""):
      $sorted = " order by $sortby ";
  else :      // Note: replace the primary key with a different default sort field name if desired...
      $sorted = " order by UserEmail ";
      $sortby = "UserEmail";
  endif;

  if (strlen($bareQuery)==0) :
      $bareQuery = "SELECT UserId,UserName,UserEmail FROM users ";
      $Title = "Registered Users: ";
      if (!is_null($UserId)) { $where .= "AND UserId = '$UserId'"; }
  endif;

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);
  $b = "";

  // The next line loads a generic display loop for the table...
  require_once("UsersListDisplay.php");

  // Create the buttons at the bottom of the form...
  $b .= "<div class='centered'><hr>\n";
  $b .= "<a class=b1 href='#' onClick='clearDiv(\"recordView\"); processAjax(\"Introduction.php\", \"columnLeft\");'>Close List</a> &nbsp; \n";
  $b .= "<a class=b1 href='#' onClick='showUserEntry(0);'>Add New User</a> &nbsp; \n";
  $b .= "</div>";
  echo $b;
  $b = "";
?>