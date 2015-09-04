<?php
  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  if (isset($_POST['CId'])) { $CompilationId = $_POST['CId']; }
  if (isset($_GET['CId'])) { $CompilationId = $_GET['CId']; }

  $bareQuery = "SELECT users.UserName, compilation2user.* FROM (compilations INNER JOIN compilation2user ON compilations.CompilationId = compilation2user.CompilationId) INNER JOIN users ON compilation2user.UserId = users.UserId ";
  $where = "WHERE compilations.CompilationId = '$CompilationId'";

  // echo "bareQuery = $bareQuery<br>\n";
  // echo "Where = $where<br>\n";

  $sorted = " ORDER BY users.UserName";
  $sortby = "Compilation2UserId";

  $Description = FetchLookup("Description", "compilations", "CompilationId", $CompilationId);
  $Title = "Users for $Description:";

  $queryall = $bareQuery.$where.$sorted;
  // echo "Query=$queryall<br>\n";
  $resultall = mysqli_query($conn, $queryall);
  $numberall = mysqli_num_rows($resultall);

  // This array lets us display the word instead of the number for n/y bytes, where 0 = No and 1 = Yes...
  $ny = array("No", "Yes");

  // The next line loads a generic display loop for the table...
  require_once("Compilation2UserListDisplay.php");
  echo $o;
  $o = "";
?>