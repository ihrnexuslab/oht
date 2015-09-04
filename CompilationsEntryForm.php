<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("settings.php");

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }

  if (isset($_GET['CId'])) { $CompilationId=$_GET['CId']; }
  if (isset($_GET['S'])) { $mySet=$_GET['S']; }

  if ($CompilationId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM compilations WHERE CompilationId = '$CompilationId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $CompilationId = $fields["CompilationId"];
      $Description = $fields["Description"];
      $Creator = $fields["Creator"];
      $DateAdded = $fields["DateAdded"];
      $UserId = $fields["UserId"];
      $Heading .= "Edit the Compilation Description";
      $Title = "Edit the record and press 'Submit'...\n";
  else :
      // This is a new record, which has not yet been submitted...
      $Heading = "Describe a New Compilation\n";
      $Title = "Fill in the form and press 'Submit'...\n";
      $UserId = $myUser;
  endif;

  $o = "";
  $o .= "<div class=centered>\n";
  $o .= "  <h3>$Heading</h3>\n";
  $o .= "  <form id='formComp' name='formComp' method=post>\n";
  $o .= "    <input type='hidden' name='CompilationId' value=$CompilationId>\n";
  $o .= "    <input type='hidden' name='UserId' value='$UserId'>\n";
  $o .= "    <input type='hidden' name='mySet' value=$mySet>\n";
  $o .= "    <input type='hidden' name='Action' Id='Action' value='Submit'>\n";
  $o .= "    <table rules=rows style='width: 600px;'>\n";
  $o .= "      <thead><tr class=tblname>\n";
  $o .= "        <td colspan=2>$Title</td>\n";
  $o .= "      </tr></thead>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Description<sup><span style='color:red'>*</span></sup> :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <textarea name='Description' rows='2' wrap='soft' style='width: 100%;' required='required'>$Description </textarea>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tfoot><tr class=footer>\n";
  $o .= "        <td colspan=2>\n";
  $o .= "            <input type='button' class='b2' id='Submit' onClick='verifyCompilation(\"formComp\")' value='Submit'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.formComp.reset()'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Cancel' value='Cancel' onclick='clearDiv(\"recordView\")'>&nbsp;&nbsp;\n";
  $o .= "            <span class=tiny> &nbsp; Fields with an asterisk(<em><span style='color:red'>*</span></em>) are required.</span>\n";
  $o .= "        </td>\n";
  $o .= "      </tr></tfoot>\n";
  $o .= "    </table>\n";
  $o .= "  </form>\n";
  $o .= "  <div class=hbar></div>\n";
  $o .= "</div>\n";
  echo $o;
  $o = "";
?>