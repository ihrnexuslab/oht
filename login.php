<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');
  require_once("settings.php");
  $l = "";
  $l .= "    $fail\n";
  $l .= "    <form id='form1' name='form1' method='post' action='loginTest.php'>\n";
  $l .= "      <table style='width:100%; font-size: 8.5pt' align='center' border = 2 rules=groups>\n";
  $l .= "        <thead><tr>\n";
  $l .= "          <th colspan='2' class='tblname'>Log In Here.</td>\n";
  $l .= "        </tr></thead>\n";
  $l .= "        <tbody><tr>\n";
  $l .= "          <td>E-mail:</td>\n";
  $l .= "          <td><input type='text' name='username' id='username' /></td>\n";
  $l .= "        </tr>\n";
  $l .= "        <tr>\n";
  $l .= "          <td>Password</td>\n";
  $l .= "          <td><input type='password' name='password' id='password' /></td>\n";
  $l .= "        </tr>\n";
  $l .= "        <tr>\n";
  $l .= "          <td></td>\n";
  $l .= "          <td><a href='#' onClick='forgotPassword(document.getElementById(\"username\").value);'>I forgot my password.</a></td>\n";
  $l .= "        </tr>\n";
  $l .= "        <tr>\n";
  $l .= "          <td>&nbsp;</td>\n";
  $l .= "          <td>\n";
  $l .= "            <input class=b1 type='button' name='Submit' id='Submit' value='Log In' onClick='submitForm(document.getElementById(\"form1\"), \"loginTest.php\", \"logIn\"); ' />\n";
  if ($AllowPublicRegistration == 1) { $l .= "           or <a href='#' class=b1 style='text-decoration: none' onClick='showRegistration(0);'>Register Here</a>\n"; }
  $l .= "          </td>\n";
  $l .= "        </tr>\n";
  $l .= "        <tbody>\n";
  $l .= "      </table>\n";
  $l .= "    </form>\n";

  // This menu form is used to store global values so the javascript can pick them up as needed.
  $l .= "    <form id='menu' name='menu'>\n";
  $l .= "      <input type='hidden' id='myRecordingId' value=0>\n";
  $l .= "      <input type='hidden' id='myTrackId' value=0>\n";
  $l .= "      <input type='hidden' id='myTranscriptId' value=0>\n";
  $l .= "      <input type='hidden' id='myAudioFile' value='none'>\n";
  $l .= "      <input type='hidden' id='myStart' value=0>\n";
  $l .= "      <input type='hidden' id='myStop' value=0>\n";
  $l .= "      <input type='hidden' id='myStartTime' value=0>\n";
  $l .= "      <input type='hidden' id='myUserId' value=0>\n";
  $l .= "      <input type='hidden' id='myUserName' value=''>\n";
  $l .= "      <input type='hidden' id='canUpload' value=$NewUserUploads>\n";
  $l .= "      <input type='hidden' id='isAdmin' value=$NewUserIsAdmin>\n";
  $l .= "      <input type='hidden' id='annotatesAll' value=$NewUserAnnotatesAll>\n";
  $l .= "      <input type='hidden' id='annotatesOwn' value=$NewUserAnnotatesOwn>\n";
  $l .= "      <input type='hidden' id='canModify' value=$NewUserModifies>\n";
  $l .= "      <input type='hidden' id='defaultPrefixCode' value=$DefaultPrefixCode>\n";
  $l .= "      <input type='hidden' id='restrictQueriesToThisInstance' value=$RestrictQueriesToThisInstance>\n";
  $l .= "      <input type='hidden' id='useCompilationsUsers' value=$UseCompilationsUsers>\n";
  $l .= "  </form>\n";

  $l .= "    <div class=center style='margin-top: 1em; margin-bottom: .5em'>\n";
  if ($DefaultCompilationId > 0) :
      $l .= "      <a href='#' class=b1 style='text-decoration: none' onClick='showRecordings($DefaultCompilationId)'>List Recordings</a> &nbsp; &nbsp;\n";
  else :
      $l .= "      <a href='#' class=b1 style='text-decoration: none' onClick='showCompilations(0,0)'>List Compilations</a> &nbsp; &nbsp;\n";
  endif;
  $l .= "    </div>\n";
  echo $l;
  $l = "";
?>