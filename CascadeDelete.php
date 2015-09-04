<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  //Start session
  session_start();

  require_once("OralHistoryDataConn.php");
  require_once("LookupFunctions.php");
  require_once("settings.php");

  if (isset($_SESSION['s_uData'])) :
      $uData = $_SESSION['s_uData'];
      $myUser = $uData['UserId'];
  endif;

  // Initialize control variables to 0.
  $C = 0;  // Compilations
  $R = 0;  // Recordings
  $T = 0;  // Tracks
  $A = 0;  // Annotations
  $TR = 0; // Transcripts
  $U = 0;  // User

  if ($myUser == 1 || ($uData['IsAdmin'] == 1 && $AdminsCanDelete == 1)) :
      // We can proceed to delete records based on what was passed in...
      $message = "<h4>An error occurred in the delete routine.</h4>";

      if (isset($_REQUEST['C'])) :
          // Compilation...Implies deletion of everything, including the audio track files.
          $C = $_REQUEST['C'];
          $Block = "compilations";
          $res = DeleteCompilation($C);
          if ($res == true):
              $message = "<h5>The compilation and all its child records have been deleted.\nThis includes all recordings, tracks, audio files, annotations, \nand transcripts for the compilation.</h5>";
          else :
              $message .= $res;
          endif;

      elseif (isset($_REQUEST['R'])) :
          // Recording...Implies deletion of everything but the parent Compilation.
          $R = $_REQUEST['R'];
          $Parent = FetchLookup("CompilationId", "recordings", "RecordingId", $R);
          $Block = "recordings";
          $res = DeleteRecording($R);
          if ($res == true):
              $message = "<h5>The recording and all its child records have been deleted.\nThis includes all tracks, audio files, annotations, \nand transcripts for the recording.</h5>";
          else :
              $message .= $res;
          endif;

      elseif (isset($_REQUEST['T'])) :
          // Track...Implies deletion of everything but the parent Compilation and Recording.
          $T = $_REQUEST['T'];
          $Parent = FetchLookup("RecordingId", "tracks", "TrackId", $T);
          $Block = "tracks";
          $res = DeleteTrack($T);
          if ($res == true):
              $message = "<h5>The track and all its child records have been deleted.\nThis includes the audio file and all annotations and transcripts\nfor the track.</h5>";
          else :
              $message .= $res;
          endif;

      elseif (isset($_REQUEST['A'])) :
          // Annotation...delete an annotation.
          $A = $_REQUEST['A'];
          $Parent = FetchLookup("TrackId", "annotation", "AnnotationId", $A);
          $Block = "annotations";
          $res = DeleteAnnotation($A);
          if ($res == true):
              $message = "<h5>The annotation has been deleted.</h5>";
          else :
              $message .= $res;
          endif;

     elseif (isset($_REQUEST['TR']) || isset($_REQUEST['U'])) :
         // Transcripts...delete transcripts for an audiotrack.
         if (isset($_REQUEST['TR'])) :
             $T = $_REQUEST['TR'];
             $Parent = FetchLookup("RecordingId", "transcripts", "TrackId", $TR);
             $user = "";
         endif;

         // Or delete all transcripts for an audiotrack that belong to one user.
         if (isset($_REQUEST['U'])) :
             $U = $_REQUEST['U'];
             $Parent = FetchLookup("RecordingId", "transcripts", "UserId", $U);
             $user = "and user ";
         endif;

         if ($TR > 0 || $U > 0) :
             $Block = "transcripts";
             $res = DeleteTranscripts($TR, $U);
             if ($res == true):
                 $message = "<h5>The transcript blocks for the track " . $user . "have been deleted.</h5>";
             else :
                 $message .= $res;
             endif;
         endif;
     endif;

     // Print the message and the appropriate listing...
     echo $message;
     switch ($Block) :
       case "compilations" :
         // The compilations list goes in the "recordView" div.
         include_once("CompilationsLister.php");
         break;
       case "recordings" :
         // The recordings list goes in the "columnLeft" div.
         $CompilationId = $Parent;
         include_once("RecordingsLister.php");
         break;
       case "tracks" :
         // The recording metadata goes in the "recordView" div.
         $RecordingId = $Parent;
         include_once("RecordingsRecordView.php");
         break;
       case "annotations" :
         // The annotations list goes in the "annotations" div.
         $TrackId = $Parent;
         include_once("AnnotationLister.php");
         break;
       case "transcripts" :
         // The recording metadata goes in the "recordView" div.
         $RecordingId = $Parent;
         include_once("RecordingsRecordView.php");
         break;
     endswitch;

  else :
    echo "<h5>You do not have permission to delete.</h5>";
  endif;

  //---------------------------------------------------------------------------------------------
  // DeleteCompilation($C) -- Deletes a Compilation, where $C is the CompilationId.
  //                          All the children are deleted first. 
  //---------------------------------------------------------------------------------------------
  function DeleteCompilation($C) {
    global $conn;
    if ($C > 0) { 
        // First delete the recordings and their children...
        $q = "SELECT RecordingId FROM recordings WHERE CompilationId = '$C'";
        $result = mysqli_query($conn, $q);
        $numrecs = mysqli_num_rows($result);
        $x=0;
        while ($x<$numrecs):
               // Retreive Data and put it in Local Variables for each Row...
               $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
               $R = $row['RecordingId'];
               DeleteRecording($R);
               $x++;
        endwhile;

        // Delete the compilation record.
        $q = "DELETE FROM compilations WHERE CompilationId = '$C'";
        if (mysqli_query($conn, $q)===false) { return "<h5>Error deleting Compilation: " . mysqli_error($conn) . "</h5>"; }
    } else { return "<h5>No Compilation to Delete.</h5>"; }
    return true;
  }

  //---------------------------------------------------------------------------------------------
  // DeleteRecording($R) -- Deletes a Recording, where $R is the RecordingId.  
  //                        All the children are deleted first. 
  //---------------------------------------------------------------------------------------------
  function DeleteRecording($R) {
    global $conn;
    if ($R > 0) { 
        // First delete the tracks and their children...
        $q = "SELECT TrackId FROM tracks WHERE RecordingId = '$R'";
        $result = mysqli_query($conn, $q);
        $numrecs = mysqli_num_rows($result);
        $x=0;
        while ($x<$numrecs):
               // Retreive Data and put it in Local Variables for each Row...
               $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
               $T = $row['TrackId'];
               DeleteTrack($T);
               $x++;
        endwhile;

        // Delete the recording record.
        $q = "DELETE FROM recordings WHERE RecordingId = '$R'";
        if (mysqli_query($conn, $q)===false) { return "<h5>Error deleting Recording: " . mysqli_error($conn) . "</h5>"; }
    } else { return "<h5>No Recording to Delete.</h5>"; }
    return true;
  }

  //---------------------------------------------------------------------------------------------
  // DeleteTrack($T) -- Deletes a Track, where $T is the TrackId.  
  //                    All the children are deleted first.  
  //---------------------------------------------------------------------------------------------
  function DeleteTrack($T) {
    global $conn;
    if ($T > 0) {
        // First delete the audio file...
        $q = "SELECT FilePath, Identifier FROM tracks WHERE TrackId = '$T'";
        $result = mysqli_query($conn, $q);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $FilePath = $row['FilePath'];
        $Identifier = stripslashes($row['Identifier']);
        $myFile = $FilePath.$Identifier;
        unlink(realpath($myFile));

        // Now delete the annotations and transcripts, and finally, the track record.
        DeleteAnnotation(0, $T);
        DeleteTranscript($T);

        $q = "DELETE FROM tracks WHERE TrackId = '$T'";
        if (mysqli_query($conn, $q)===false) { return "<h5>Error deleting Track: " . mysqli_error($conn) . "</h5>"; }
    } else { return "<h5>No Track to Delete.</h5>"; }
    return true;
  }

  //---------------------------------------------------------------------------------------------
  // DeleteAnnotation($A, $T) -- Deletes an Annotation, where $A = the AnnotationId, or $A=0 
  //                             & $T is the TrackId (in which case all the annotations for the 
  //                             track are deleted.)
  //---------------------------------------------------------------------------------------------
  function DeleteAnnotation($A, $T=0) {
    global $conn;
    if ($A > 0 || $T > 0) {
        if ($A > 0) { $q = "DELETE FROM annotation WHERE AnnotationId = '$A'"; }
        if ($T > 0) { $q = "DELETE FROM annotation WHERE TrackId = '$T'"; }
        if (mysqli_query($conn, $q)===false) { return "<h5>Error deleting Annotation: " . mysqli_error($conn) . "</h5>"; }
    } else { return "<h5>No Annotation to Delete.</h5>"; }
    return true;
  }

  //---------------------------------------------------------------------------------------------
  // DeleteTranscript($T, $U=0) -- Deletes all transcriptions of an audio track made by the user
  //                               where $T is the TrackId and $U is the UserId. If $U = 0 all
  //                               transcriptions for the track will be deleted regardless of
  //                               the UserId.
  //---------------------------------------------------------------------------------------------
  function DeleteTranscript($T, $U=0) {
    global $conn;
    if ($T > 0 || $U > 0) {
        $q = "DELETE FROM transcripts WHERE TrackId = '$T'";
        if ($U > 0) { $q .= " AND UserId = '$U'"; }
        if (mysqli_query($conn, $q)===false) { return "<h5>Error deleting transcripts : " . mysqli_error($conn) . "</h5>"; }
    } else { return "<h5>No Transcript to Delete.</h5>"; }
    return true;
  }

?>