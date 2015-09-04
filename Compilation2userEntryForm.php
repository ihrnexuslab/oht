<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
// & ~E_NOTICE
  ini_set("display_errors", 1);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");
?>
<SCRIPT TYPE="text/javascript">
<!-- 
//------------------------------------------------------------------------------------
  function verify() {
    var themessage = "You must enter the following fields: ";
    // Put in the required fields like this:
    // if (document.form.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    if (themessage == "You must enter the following fields: ") {
        document.form.Action.value = 'Submit'; 
        document.form.submit();
    } else {
        alert(themessage);
        return false;
    }
  }
//  End -->
</SCRIPT>
<?php

  ob_start();
  session_start();

  if (isset($_SESSION['s_uData'])) { $uData = $_SESSION['s_uData']; }
  if ($uData['UserId'] > 0) { $myUser = $uData['UserId']; }

  $DateMod = date('Y-m-d');

  $o = "";

  // Check to see if the form was submitted, and if it was, write the record...
  if (isset($_POST['Action']) && $_POST['Action'] == 'Submit') :

     // Fetch the variables from the $_POST array and strip any html or php tags...
   $Compilation2UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['Compilation2UserId']));
   $CompilationId = strip_tags(mysqli_real_escape_string($conn, $_POST['CompilationId']));
   $UserId = strip_tags(mysqli_real_escape_string($conn, $_POST['UserId']));
   $IsAdmin = strip_tags(mysqli_real_escape_string($conn, $_POST['IsAdmin']));
   $CanUpload = strip_tags(mysqli_real_escape_string($conn, $_POST['CanUpload']));
   $CanAnnotate = strip_tags(mysqli_real_escape_string($conn, $_POST['CanAnnotate']));
   $CanDownload = strip_tags(mysqli_real_escape_string($conn, $_POST['CanDownload']));
   $CanAdd = strip_tags(mysqli_real_escape_string($conn, $_POST['CanAdd']));
   $CanModify = strip_tags(mysqli_real_escape_string($conn, $_POST['CanModify']));
   $EnteredBy = strip_tags(mysqli_real_escape_string($conn, $_POST['EnteredBy']));
   $DateEntered = strip_tags(mysqli_real_escape_string($conn, $_POST['DateEntered']));

   // Determine whether to update or insert the record, based on the value of the primary key...
   // Check for an existing record based on the positive value of the primary key.
   // If the record exists, fetch it--we are in edit record mode.
   // If the key value is less than 1, then the record does not exist and we are in add mode.

   if ($Compilation2UserId > 0) :
        $query="UPDATE compilation2user SET Compilation2UserId='$Compilation2UserId',CompilationId='$CompilationId',UserId='$UserId',IsAdmin='$IsAdmin',CanUpload='$CanUpload',CanAnnotate='$CanAnnotate',CanDownload='$CanDownload',CanAdd='$CanAdd',CanModify='$CanModify',EnteredBy='$EnteredBy',DateEntered='$DateEntered' WHERE Compilation2UserId = '$Compilation2UserId'";
        $Heading = "Updated Compilation2user Record.";
   else :
        // Insert the MySQL record...
        // The Primary Key value is a sequential number...
        $Compilation2UserId = TheNextKeyValue('Compilation2UserId','compilation2user');
        $query="INSERT INTO compilation2user (Compilation2UserId,CompilationId,UserId,IsAdmin,CanUpload,CanAnnotate,CanDownload,CanAdd,CanModify,EnteredBy,DateEntered) VALUES ('$Compilation2UserId','$CompilationId','$UserId','$IsAdmin','$CanUpload','$CanAnnotate','$CanDownload','$CanAdd','$CanModify','$EnteredBy','$DateEntered')";
        $Heading = "Added Compilation2user Record.";
   endif;
   $result = mysqli_query($conn, $query);

   // Check result
   // This shows the actual query sent to MySQL, and the error. Useful for debugging.
   if (!$result) :
       $message  = 'Invalid query: ' . mysqli_error($conn);
       $message .= 'Whole query: ' . $query;
       die($message);
   endif;
else:
   // The form was not submitted; get the primary key value...
   $Compilation2UserId=$_GET['Compilation2UserId'];
endif;

if ($Compilation2UserId > 0) :
    // This is an existing record, so read it...
    $query="SELECT * FROM compilation2user WHERE Compilation2UserId = '$Compilation2UserId'";
    $result = mysqli_query($conn, $query);
    $fields = mysqli_fetch_assoc($result);
    $Compilation2UserId = $fields["Compilation2UserId"];
    $CompilationId = $fields["CompilationId"];
    $UserId = $fields["UserId"];
    $IsAdmin = $fields["IsAdmin"];
    $CanUpload = $fields["CanUpload"];
    $CanAnnotate = $fields["CanAnnotate"];
    $CanDownload = $fields["CanDownload"];
    $CanAdd = $fields["CanAdd"];
    $CanModify = $fields["CanModify"];
    $EnteredBy = $fields["EnteredBy"];
    $DateEntered = $fields["DateEntered"];
    $Title .= "Edit Record # $Compilation2UserId...";
else :
    // This is a new record, which has not yet been submitted...
    $Heading = "Enter a New Compilation2user Record!\n";
    $Title = "Fill in the form and press 'Submit'...\n";
endif;

$o .= "<div class=centered>\n";
$o .= "  <h2>$Heading</h2>\n";
$o .= "<form name=form method=post action='$PHP_SELF'>\n";
$o .= "    <input type='hidden' name='Compilation2UserId' value=\'$Compilation2UserId\'>\n";
$o .= "    <input type='hidden' name='CompilationId' value='$CompilationId'>\n";
$o .= "    <input type='hidden' name='EnteredBy' value='$EnteredBy'>\n";
$o .= "    <input type='hidden' name='DateEntered' value='$DateEntered'>\n";
$o .= "    <input type='hidden' name='Action' value='Submit'>\n";
$o .= "    <table rules=rows>\n";
$o .= "      <thead><tr class=tblname>\n";
$o .= "        <td colspan=2>$Title</td>\n";
$o .= "      </tr></thead>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row0>\n";
$o .= "          <td class=caption>UserId :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('UserId','users','UserId','UserName','List','UserName',$UserId);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row1>\n";
$o .= "          <td class=caption>IsAdmin :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('IsAdmin','users','IsAdmin','UserName','ChkYes','UserName',$IsAdmin);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row0>\n";
$o .= "          <td class=caption>CanUpload :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('CanUpload','users','CanUpload','UserName','ChkYes','UserName',$CanUpload);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row1>\n";
$o .= "          <td class=caption>CanAnnotate :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('CanAnnotate','users','CanAnnotate','UserName','ChkYes','UserName',$CanAnnotate);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row0>\n";
$o .= "          <td class=caption>CanDownload :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('CanDownload','users','CanDownload','UserName','ChkYes','UserName',$CanDownload);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row1>\n";
$o .= "          <td class=caption>CanAdd :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('CanAdd','users','CanAdd','UserName','ChkYes','UserName',$CanAdd);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tbody>\n";
$o .= "        <tr class=row0>\n";
$o .= "          <td class=caption>CanModify :</td>\n";
$o .= "          <td>\n";
$o .=              MakeLookup('CanModify','users','CanModify','UserName','ChkYes','UserName',$CanModify);
$o .= "          </td>\n";
$o .= "        </tr>\n";
$o .= "      </tbody>\n";
$o .= "      <tfoot><tr class=footer>\n";
$o .= "        <td colspan=1>\n";
$o .= "          <input type='submit' id='SubmitBtn' name='Submit' class=fbutton value='Submit' onMouseOver='beLite(this.id)' onMouseOut='beDim(this.id)' onMouseDown='beDown(this.id)' onMouseUp='verify(); beDim(this.id)'>&nbsp;\n";
$o .= "          <input type='reset'  id='ResetBtn' name='Submit2' class=fbutton value='Reset' onMouseOver='beLite(this.id)' onMouseOut='beDim(this.id)' onMouseDown='beDown(this.id)' onMouseUp='document.form.reset; beDim(this.id)'>\n";
$o .= "        </td>\n";
$o .= "        <td colspan=5><p class=bib>Fields with an asterisk(<em><span style='color:red'>*</span></em>) are required.</p></td>\n";
$o .= "      </tr></tfoot>\n";
$o .= "    </table>\n";
$o .= "  </form>\n";
$o .= "</div>\n";
$o .= "<hr>\n";
if ($Compilation2UserId>0):
    $o .= "<a class=button style='width: 100px; text-decoration: none;' href='$fullPath/Compilation2userEntryForm.php' target='_top'>Add Another</a>&nbsp;&nbsp;";
endif;
$o .= "<a class=button style='width: 100px; text-decoration: none;' href='$fullPath/index.php' target='_top'>Main Menu</a>&nbsp;&nbsp;";
echo $o;
require_once("footer.php");
?>
</body>
</html>
