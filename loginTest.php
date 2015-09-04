<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("settings.php");
  ob_start();
  session_start();
    
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $where = "WHERE PrefixCode = '$DefaultPrefixCode' ";

  $query = "SELECT * FROM users WHERE UserEmail = '$username' ORDER BY UserId";
  $result = mysqli_query($conn, $query);
  if(mysqli_num_rows($result) == 0) : 
     echo "User E-mail not found!<br>";
     // User not found. So, redirect to login_form again.
     require_once("login.php");
  else :
     // The user's e-mail is found, do the passwords match?
     $uData = mysqli_fetch_assoc($result);
     $salt = $uData['Salt'];
     $hash = sha1($salt.$password);
     $NumLogIns = $uData['NumLogIns'];

     // Un-comment the next two lines to debug the password entry and hashing...
     // print_r($uData);
     // echo "<br>$password<br>$salt<br>$hash<br>" . $uData['UserPw'] . "<br>\n";

     // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
     // The system administrator has UserId = 1, so if the UserId > 1 it is not the system admin,     *
     // and we must check to be sure they are in the right instance of the web app, which can be      *
     // confirmed if the user's PrefixCode value matches the $DefaultPrefixCode from settings.php.    *
     // So, if the UserId > 1 and the user' PrefixCode doesn't match the $DefaultPrefixCode, the user *
     // fails this test; thus, they are invalid.                                                      *
     // OR:                                                                                           *
     // If the hash of the login password doesn't match the UserPw value, then it is an invalid user. *
     // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 

     if(($uData['UserId'] > 1 && $uData['PrefixCode'] != $DefaultPrefixCode) || ($hash != $uData['UserPw'])) :
        $_SESSION['s_attempts'] = $_SESSION['s_attempts'] + 1;
        $fail = "<h5>Username/password not found.</h5>";
        require_once("login.php");
     else :
        // Build menu page after successful login.
        session_regenerate_id();
        $_SESSION['s_user_id'] = $uData['UserId'];
        $_SESSION['s_email'] = $uData['UserEmail'];
        $_SESSION['s_username'] = $uData['UserName'];
        $UserId = $uData['UserId'];
        $_SESSION['s_uData'] = $uData;

        // Update the user's login stats.
        $NumLogIns++;
        $LastLogIn = date("Y-m-d H:i:s");
        $query="UPDATE users SET LastLogIn='$LastLogIn', NumLogIns='$NumLogIns' WHERE UserId = '$UserId'";
        $result = mysqli_query($conn, $query);

        session_write_close();
        // Successful login...
        $l  = "";
        $fail = "";
        if ($DefaultCompilationId > 0) :
            $b .= "<a href='#' class=b5 style='text-decoration: none' onClick='showRecordings($DefaultCompilationId)'>List Recordings</a>\n";
        else :
            $b .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showCompilations(0,0)'>List All Compilations</a><br>\n";
            if ($UseCompilationsUsers == 1) :
                $b .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showUserCompilations($UserId);'>List My Compilations</a><br>\n";
            else:
                $b .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showCompilations($UserId,$UserId);'>List My Compilations</a><br>\n";
            endif;
        endif;
        $l .= "<div class=center>\n";
        // This menu form is used to store global values so the javascript can pick them up as needed.
        $l .= "  <form id='menu' name='menu'>\n";
        $l .= "    <input type='hidden' id='myRecordingId' value=0>\n";
        $l .= "    <input type='hidden' id='myTrackId' value=0>\n";
        $l .= "    <input type='hidden' id='myTranscriptId' value=0>\n";
        $l .= "    <input type='hidden' id='myAudioFile' value='none'>\n";
        $l .= "    <input type='hidden' id='myStart' value=0>\n";
        $l .= "    <input type='hidden' id='myStop' value=99999>\n";
        $l .= "    <input type='hidden' id='myStartTime' value=0>\n";
        $l .= "    <input type='hidden' id='myUserId' value=" . $uData['UserId'] . ">\n";
        $l .= "    <input type='hidden' id='myUserName' value=" . $uData['UserName'] . ">\n";
        $l .= "    <input type='hidden' id='canUpload' value=" . $uData['CanUpload'] . ">\n";
        $l .= "    <input type='hidden' id='isAdmin' value=" . $uData['IsAdmin'] . ">\n";
        $l .= "    <input type='hidden' id='annotatesAll' value=" . $uData['AnnotatesAll'] . ">\n";
        $l .= "    <input type='hidden' id='annotatesOwn' value=" . $uData['AnnotatesOwn'] . ">\n";
        $l .= "    <input type='hidden' id='canModify' value=" . $uData['CanModify'] . ">\n";
        $l .= "    <input type='hidden' id='defaultPrefixCode' value=$DefaultPrefixCode>\n";
        $l .= "    <input type='hidden' id='restrictQueriesToThisInstance' value=$RestrictQueriesToThisInstance>\n";
        $l .= "    <input type='hidden' id='useCompilationsUsers' value=$UseCompilationsUsers>\n";
        $l .= "  </form>\n";
        if ($uData['IsAdmin'] == 1) :
            $l .= "<h5>You are logged in as " . $uData['UserName'] . ".<br>Select an Administrator Option Below:</h5>\n";
            $l .= $b;
            if ($DefaultCompilationId == 0) { $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showCompEntry(0);'>Add New Compilation</a><br>\n"; }
            $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showUserView($UserId);'>View My Profile</a><br>\n";
            $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='processAjax(\"UsersLister.php\", \"columnLeft\");'>List All Users</a><br>\n";
            $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showUserEntry(0);'>Add a New User</a><br>\n";
        else :
            $l .= "<h5>You are logged in as " . $uData['UserName'] . ".<br>Select a User Option Below:</h5>\n";
            $l .= $b;
            if ($uData['CanUpload'] == 1 && $DefaultCompilationId == 0) :
                $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showCompEntry(0);'>Add New Compilation</a><br>\n";
            endif;
            $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showUserProfile($UserId);'>View My Profile</a><br>\n";
        endif;
        $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='showPWReset($UserId);'>Change My Password</a><br>\n";
        $l .= "<a href='#' class=b5 style='text-decoration: none;' onClick='logOut()'>Log Out</a><br>\n";
        $l .= "</div>\n";
        $l .= "<div id='logInMsg'></div>\n";
        echo $l;
        $l = "";
     endif;  // hash loop...
     $b = mysqli_free_result($result);
  endif; // result loop...
?>