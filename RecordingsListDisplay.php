<?php
  $o = "";
  if ($numberall==0) :
      $o .= "<div class=centered>\n";
      $o .= "  <h3>No Records Found!</h3>\n";
      if ($uData['CanUpload'] == 1 || $uData['IsAdmin'] == 1) :
          $o .= "  <a href='#' class='b2' onClick='showRecordEntry(0, $CompilationId);' style='margin-top: 1em; text-decoration:none'>Add a New Recording to this Compilation</a> &nbsp;\n";
      endif;
      $o .= "</div>\n";
  elseif ($numberall>0) :
      $o .= "<div class=centered>\n";
      $o .= "    <a href='#' class=b1 style='text-decoration:none; margin-top:.5em' onClick='processAjax (\"Introduction.php\", \"columnLeft\"); clearDiv(\"keyWord\");'>Close Recordings List</a> &nbsp; \n";
      if ($uData['CanUpload'] == 1 || $uData['IsAdmin'] == 1) :
          $o .= "    <a href='#' class='b2' onClick='showRecordEntry(0, $CompilationId);' style='margin-top: 1em; text-decoration:none'>Add a New Recording to this Compilation</a> &nbsp;\n";
      endif;
      $o .= "  <table rules=groups style='width: 800px; font-size: 8pt; margin-top: 1em;'>\n";
      $o .= "    <tr class=tblname>\n";
      $o .= "      <td colspan=2+$ShowRecordId>Recordings in $myTitle: </td>\n";
      $o .= "    </tr>\n";
      $o .= "    <tr>\n";
      if ($ShowRecordId == 1) :
          $o .= "      <th class='colname'>RecordId</th>\n";
      endif;
      $o .= "      <th class='colname'>Recording Interviewee/Title</th>\n";
      $o .= "      <th class='colname'>Actions</th>\n";
      $o .= "  </tr>\n";
      $x=0;

      while ($x<$numberall):
        // Retreive Data and put it in Local Variables for each Row...
        $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
        $RecordingId = $row['RecordingId'];
        $Title = stripslashes($row['Title']);
        $UserId = stripslashes($row['UserId']);
        $Publish = stripslashes($row['Publish']);
        // We can show the record under three different conditions:
        // 1. It's Publish flag is set to 1 (true);
        // 2. The logged in user is the owner of the record;
        // 3. The logged in user is an administrator.
        if (($Publish == "1") || ($UserId == $myUser) || ($uData['IsAdmin'] == 1)) : 
           // Create Output Row...
           $rowclass='row' . ($x%2);        // Change Background color for each alternate row...
           $o .= "      <tbody><tr class=$rowclass>\n";
           if ($ShowRecordId == 1) :
               $o .= "      <td>$RecordingId</td>\n";
           endif;
           $o .= "        <td>$Title </td>\n";
           $o .= "        <td class=center>\n";
           $o .= "          <a href='#' onClick='showRecordView($RecordingId);'>Metadata</a>&nbsp;&nbsp;\n";
           if ($uData['CanUpload'] == 1 || $uData['IsAdmin'] == 1) :
               $o .= "    <a href='#' onClick='showTrackEntry(0, $RecordingId);'>Upload a New Track</a>\n";
           endif;
           // The Superuser has UserId = 1, so they can delete the record...
           if ($myUser == 1 || ($IsAdmin == 1 && $AdminsCanDelete == 1)) :
               $o .= "     &nbsp; <a href='#' style='color:red' onClick='delRecord(\"columnLeft\",\"R=$RecordingId\");'><span style='color:red'>Delete</span></a>\n";
           endif;
           $o .= "        </td>\n";
           $o .= "      </tr></tbody>\n";
           $x++;
        endif;
      endwhile;
      // Close Table...
      $o .= "    </table>\n";
      if (DefaultCompilationId == 0) { $o .= "    <br><a href='#' class=b1 style='text-decoration:none; margin-top:.5em' onClick='showCompilations(0,0);'>List Compilations</a> &nbsp; \n"; }
      $o .= "    <a href='#' class=b1 style='text-decoration:none; margin-top:.5em' onClick='processAjax (\"Introduction.php\", \"columnLeft\"); clearDiv(\"keyWord\");'>Close Recordings List</a> &nbsp; \n";
      if ($uData['CanUpload'] == 1 || $uData['IsAdmin'] == 1) :
          $o .= "    <a href='#' class='b2' onClick='showRecordEntry(0, $CompilationId);' style='margin-top: 1em; text-decoration:none'>Add a New Recording to this Compilation</a> &nbsp; \n";
      endif;
      $o .= "</div>\n";
  endif;
?>