<?php
  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  $DateMod = date('Y-m-d');

  $o = "";

  // The form was not submitted; get the primary key value...
  $UserId=$_GET['U'];

  if ($UserId > 0) :
      // This is an existing record, so read it...
      $query="SELECT * FROM users WHERE UserId = '$UserId'";
      $result = mysqli_query($conn, $query);
      $fields = mysqli_fetch_assoc($result);
      $UserId = $fields["UserId"];
      $UserName = $fields["UserName"];
      $UserAddress = $fields["UserAddress"];
      $UserPhone = $fields["UserPhone"];
      $UserMobilePhone = $fields["UserMobilePhone"];
      $UserFax = $fields["UserFax"];
      $UserEmail = $fields["UserEmail"];
      $UserURL = $fields["UserURL"];
      $CountryID = $fields["CountryID"];
      $UserPwReminder = $fields["UserPwReminder"];
      $IsAdmin = $fields["IsAdmin"];
      $CanUpload = $fields["CanUpload"];
      $AnnotatesOwn = $fields["AnnotatesOwn"];
      $AnnotatesAll = $fields["AnnotatesAll"];
      $CanDownload = $fields["CanDownload"];
      $CanAdd = $fields["CanAdd"];
      $CanModify = $fields["CanModify"];
      $EnteredBy = $fields["EnteredBy"];
      $DateEntered = $fields["DateEntered"];
      $Title .= "Edit $UserName's Record...";
      $Heading = "";
  else :
      // This is a new record, which has not yet been submitted...
      $Heading = "If you have registered previously, please press 'Cancel' and use Log In instead."; 
      $Title = "New User Registration.  Fill in the form and press 'Submit'...\n";
  endif;

  $o .= "<body onLoad='showRecaptcha();'><div class=centered>\n";
  if (strlen($Heading) > 0) { $o .= "  <h4 style='color:#1F3F5E'>$Heading</h4>\n"; }
  $o .= "  <form name=formReg id=formReg method=post>\n";
  $o .= "    <input type='hidden' name='UserId' value=\'$UserId\'>\n";
  $o .= "    <input type='hidden' name='Salt' value='$Salt'>\n";
  $o .= "    <input type='hidden' name='EnteredBy' value='$EnteredBy'>\n";
  $o .= "    <input type='hidden' name='DateEntered' value='$DateEntered'>\n";
  $o .= "    <input type='hidden' name='Action' value=''>\n";
  $o .= "    <table rules=rows class=rv style='width: 100%; font-size: 9pt;'>\n";
  $o .= "      <thead><tr class=colname>\n";
  $o .= "        <td colspan=6>$Title</td>\n";
  $o .= "      </tr></thead>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption style='width:140px'>Name<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=2>\n";
  $o .= "            <input type='text' style='width:200px' name='UserName' id='UserName' value='$UserName' >\n";
  $o .= "          </td>\n";
  $o .= "          <td class=caption>Country :</td>\n";
  $o .= "          <td colspan=2>\n";
  $o .=              MakeLookup('CountryID','countries','CountryID','Name','List','Name',$CountryID);
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Address<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=5>\n";
  $o .= "            <textarea name='UserAddress' wrap='SOFT' rows=3 style='width: 98%;' >$UserAddress</textarea>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption>Phone<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input type='text' style='width:90px' name='UserPhone' id='UserPhone' value='$UserPhone' onChange='isPhoneNumber(this.id); return false'>\n";
  $o .= "          </td>\n";
  $o .= "          <td style='width:240px'>\n";
  $o .= "            <span class='mini'> North America Format: (555) 555-1234.<br>International Format: digits 0-9 only.</span>\n";
  $o .= "          </td>\n";
  $o .= "          <td class=caption>Mobile :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input type='text' style='width:90px' name='UserMobilePhone' id='UserMobilePhone' value='$UserMobilePhone' onChange='isPhoneNumber(this.id); return false'>\n";
  $o .= "          </td>\n";
  $o .= "          <td style='width:200px'>\n";
  $o .= "            <span class='mini'> North America Format: (555) 555-1234.<br>International Format: digits 0-9 only.</span>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  $o .= "          <td class=caption>Fax :</td>\n";
  $o .= "          <td>\n";
  $o .= "            <input type='text' style='width:90px' name='UserFax' id='UserFax' value='$UserFax' onChange='isPhoneNumber(this.id); return false'>\n";
  $o .= "          </td>\n";
  $o .= "          <td style='width:200px'>\n";
  $o .= "             <span class='mini'> North America Format: (555) 555-1234.<br>International Format: digits 0-9 only.</span>\n";
  $o .= "          </td>\n";
  $o .= "          <td class=caption>E-mail<span style='color:red'>*</span> :</td>\n";
  $o .= "          <td colspan=3>\n";
  $o .= "            <input type='text' name='UserEmail' id='UserEmail' value='$UserEmail'  onChange='checkEmail(this.id)' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td class=caption>URL :</td>\n";
  $o .= "          <td colspan=5>\n";
  $o .= "            <input type='text' style='width:700px' name='UserURL' id='UserURL' value='$UserURL' onChange='isURL(this.id)' >\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row0>\n";
  if ($UserId < 1 || is_null($UserId)) :
      // Allow password fields...
      $o .= "          <td class=caption>Password<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td><input type='password' name='UserPw' value='' ></td>\n";
      $o .= "          <td class=caption>Pw Reminder<span style='color:red'>*</span> :</td>\n";
      $o .= "          <td colspan=3><input type='text' style='width:392px' name='UserPwReminder' id='UserPwReminder' value='$UserPwReminder'></td>\n";
  else :
      $o .= "          <td class=caption>Password :</td>\n";
      $o .= "          <td>Not Retreivable</td>\n";
      $o .= "          <td class=caption>Pw Reminder :</td>\n";
      $o .= "          <td colspan=3>$UserPwReminder</td>\n";
  endif;
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";
  $o .= "      <tbody>\n";
  $o .= "        <tr class=row1>\n";
  $o .= "          <td colspan=6>\n";
  $o .= "            <div id=\"captchadiv\"></div>\n";
  $o .= "          </td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tbody>\n";

  $o .= "      <tfoot>\n";
  $o .= "        <tr class=colname>\n";
  $o .= "          <td colspan=6 style='vertical-align:middle'>\n";
  $o .= "            <input type='button' class='b2' id='Submit' value='Submit' onClick='verifyUser(\"formReg\",$UserId,1); return false;'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' id='Reset' value='Reset' onclick='document.form.reset()'>&nbsp;&nbsp;\n";
  $o .= "            <input type='button' class='b2' onClick=\"clearDiv('recordView');\" value='Cancel'a> &nbsp; \n";
  $o .= "            <span class=bib>Fields with an asterisk(<span style='color:red'>*</span>) are required.</span></td>\n";
  $o .= "        </tr>\n";
  $o .= "      </tfoot>\n";
  $o .= "    </table>\n";
  $o .= "  </form>\n";
  $o .= "  <a class=b1 href='#' onClick='clearDiv(\"recordView\");'>Close</a> &nbsp; \n";
  $o .= "</div>\n";
  $o .= "<hr style='color:#1F3F5E;background-color:#1F3F5E;height:1px;border:none;'></body>\n";
  echo $o;
?>
<script src='javascripts/xmlhttp.js' language='javascript' type='text/javascript'></script>
<script src='javascripts/validate.js' language='javascript' type='text/javascript'></script>