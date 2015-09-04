<?php
require_once("OralHistoryDataConn.php");
require_once("settings.php");

  $DateMod = date('Y-m-d');

  $UserId=$_GET['U'];
  $o = "";
  $o .= "<div class=centered>\n";
  if ($UserId > 0) :
      // This is an existing record, so read it...
      $query="SELECT UserName, UserPw, UserPwReminder FROM users WHERE UserId = '$UserId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $UserName = $fields["UserName"];
      $UserPw = $fields["UserPw"];
      $UserPwReminder = $fields["UserPwReminder"];
      $Title .= "Reset $UserName's Password...";
      $o .= "  <form name=formPW id=formPW method=post>\n";
      $o .= "    <table style='width:250px; font-size: 8pt' align='center' border = 2 rules=groups>\n";
      $o .= "      <thead><tr class=tblname>\n";
      $o .= "        <td colspan=2>$Title</td>\n";
      $o .= "      </tr></thead>\n";
      $o .= "      <tbody>\n";
      $o .= "        <tr class=row0>\n";
      $o .= "          <td>New Password<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td>\n";
      $o .= "            <input type='password' name='UserPw' id='UserPw' value='' required >\n";
      $o .= "          </td>\n";
      $o .= "        </tr>\n";
      $o .= "        <tr class=row0>\n";
      $o .= "          <td>Retype Password<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td>\n";
      $o .= "            <input type='password' name='UserPw2' id='UserPw2' value='' required >\n";
      $o .= "          </td>\n";
      $o .= "        </tr>\n";
      $o .= "        <tr class=row0>\n";
      $o .= "          <td>Password Reminder<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td>\n";
      $o .= "            <input type='text' name='UserPwReminder' id='UserPwReminder' value='' required>\n";
      $o .= "          </td>\n";
      $o .= "        </tr>\n";
      $o .= "      </tbody>\n";
      $o .= "      <input type='hidden' name='Action' value=''>\n";
      $o .= "      <input type='hidden' name='UserId' value='$UserId'>\n";
      $o .= "      <tfoot><tr class=footer>\n";
      $o .= "        <td colspan=2 align='center' >\n";
      $o .= "            <input type='button' class='b2' id='Submit' value='Submit' onClick='verifyPW(this.form);'>&nbsp;&nbsp;\n";
      $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.formPW.reset()'>&nbsp;&nbsp;\n";
      $o .= "        </td></tr>\n";
      $o .= "        <tr class=footer align='center' ><td colspan=2><span class=mini>Fields with an asterisk(<em><span style='color:red'>*</span></em>) are required.</span></td>\n";
      $o .= "      </tr></tfoot>\n";
      $o .= "    </table>\n";
      $o .= "  </form>\n";
  else :
      $o = "  <p class=mini>No User was passed to the Reset Password function.</p>\n";   
  endif;
  $o .= "  <br><input type='button' class='b1' onClick='clearDiv(\"recordView\"); clearDiv(\"logInMsg\");' value='Cancel'> &nbsp; \n";
  $o .= "</div>\n";
  echo $o;
?>