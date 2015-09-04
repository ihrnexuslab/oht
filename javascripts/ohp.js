// ohp.js -- javascript routines for the Oral History Project...

var ohp = {
    myRecordingId:0,
    myTrackId:0,
    myUserId:0,
    myUserName:null,
    myTranscriptId:0,
    myAudioFile:"none",
    myLength:0,
    myStart:0,
    myStop:99999,
    canUpload:0,
    isAdmin:0,
    annotatesAll:0,
    annotatesOwn:0,
    defaultPrefixCode:"",
    restrictQueriesToThisInstance:null,
    useCompilationsUsers:null,
    startTime:0
};

var autoLoop = 0;
var currentTrack = 0;

// The compilationPermissions object will contain user permissions for the specific compilation
// It will be used if the $UseCompilationsUsers flag = 1 in settings.php.
var compilationPermissions = {
    isAdmin:0,
    canUpload:0,
    canAnnotate:0,
    canDownload:0,
    canAdd:0,
    canModify:0
};

//--------------------------------------------------------------------------------
// initOHP() -- loads all the starting bits and pieces into their proper divs
//--------------------------------------------------------------------------------
function initOHP() {
  processAjax ("login.php", "logIn");
  processAjax ("RecordingsSearchForm.php", "kwSearch");
  processAjax ("Introduction.php", "columnLeft");
  processAjax ("footer.php", "footer");
  hidePlayer();
}

//--------------------------------------------------------------------------------
// clearAll() -- clears all the divs and loads the starting introduction
//--------------------------------------------------------------------------------
function clearAll() {
  hidePlayer();
  clearDiv("recordView");
  clearDiv("annotations");
  clearDiv("kwSearch");
  clearDiv("keyWord");
  processAjax ("Introduction.php", "columnLeft");
}

//--------------------------------------------------------------------------------
// hidePlayer() -- hides the player and annotations divs
//--------------------------------------------------------------------------------
function hidePlayer() {
  setBlockVis("annotations", "none");
  setBlockVis("btnAnnotate", "none");
  if (hideSegmentControls == true) { setBlockVis("segmentation", "none"); }
  setBlockVis("player", "none");
}

//--------------------------------------------------------------------------------
// showPlayer() -- shows the player and annotations divs
//--------------------------------------------------------------------------------
function showPlayer() {
  setBlockVis("player", "block");
  setBlockVis("annotations", "block");
  if (hideSegmentControls == true) { setBlockVis("segmentation", "none"); }

  // Only display the Annotation button if the user is logged in (where ohp.myUserId > 0)...
  if (ohp.myUserId > 0) { setBlockVis("btnAnnotate", "inline-block");}
}

//-----------------------------------------------------------------------------------
// showCompEntry(myCId) -- calls the Compilation entry form for CompilationId: myCId
//                         if myCId = 0 then it will open a blank form. If mySet is 0 
//                         then when the listing is called after a record update it 
//                         will show all the compilations.
//-----------------------------------------------------------------------------------
function showCompEntry(myCId, mySet) {
  if (myCId === null) { 
      return false; 
  } else {
      clearDiv("recordView");
      var myURL = "CompilationsEntryForm.php?CId=" + myCId + "&S=" + mySet;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//-----------------------------------------------------------------------------------
// showCompilations(myUId, mySet) -- calls the Compilation list for User: myUId
//                         if mySet is 0 then it will show them all.
//-----------------------------------------------------------------------------------
function showCompilations(myUId, mySet) {
  clearDiv("recordView");
  var myURL = "CompilationsLister.php?U=" + myUId + "&S=" + mySet;
  processAjax (myURL, "recordView");
  setBlockVis("recordView", "block");
  return true;
}

//-----------------------------------------------------------------------------------
// showUserCompilations(myUId) -- calls the Compilation list for User: myUId by
//                                passing through the Compilations2Users table,
//                                and picks up the permissions for each compilation.
//-----------------------------------------------------------------------------------
function showUserCompilations(myUId) {
  clearDiv("recordView");
  var myURL = "UserCompilationsLister.php?U=" + myUId;
  processAjax (myURL, "recordView");
  setBlockVis("recordView", "block");
  return true;
}

//-----------------------------------------------------------------------------------
// showRecordings(myCId, myPermissions) -- Lists recordings for Compilation: myCId
//                          and places them in columnLeft div. myPermissions is
//                          an array of optional permission flags that are read
//                          from the UserCompilationsLister.php script if the
//                          $UseCompilationsUsers flag = 1 in settings.php.
//-----------------------------------------------------------------------------------
function showRecordings(myCId, myPermissions) {
  var myURL = "RecordingsLister.php?CId=" + myCId;
  if (myPermissions) {
      compilationPermissions.isAdmin = myPermissions["IsAdmin"];
      compilationPermissions.canUpload = myPermissions["CanUpload"];
      compilationPermissions.canAnnotate = myPermissions["CanAnnotate"];
      compilationPermissions.canDownload = myPermissions["CanDownload"];
      compilationPermissions.canAdd = myPermissions["CanAdd"];
      compilationPermissions.canModify = myPermissions["CanModify"];
  }
  // Reset the looping controls...
  autoLoop = 0;
  currentTrack = 0;

  // Put the record in the columnLeft div...
  clearDiv("columnLeft");
  processAjax (myURL, "columnLeft");
  setBlockVis("columnLeft", "block");
  return true;
}

//--------------------------------------------------------------------------------
// showTrackView(myId) -- calls the track record view for TrackId: myTId
//                        and displays the results in the recordView div
//--------------------------------------------------------------------------------
function showTrackView(myTId) {
  if (myTId < 1) { 
      return false; 
  } else {
      clearDiv("recordView");
      ohp.myTrackId = myTId;
      var myURL = "TracksRecordView.php?TId=" + myTId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//----------------------------------------------------------------------------------------
// showRecordEntry(myRId, myCID) -- calls the recording entry form for recordingId: myRId
//                 if myRId = 0 then it will open a blank form to create a new recording.
//                 myCId is the Compilation primary key.
//----------------------------------------------------------------------------------------
function showRecordEntry(myRId, myCId) {
  if (myRId === null || myCId === null) { 
      return false; 
  } else {
      clearDiv("recordView");
      ohp.myRecordingId = myRId;
      var myURL = "RecordingsEntryForm.php?RId=" + myRId + "&CId=" + myCId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// showTrackEntry(myTId, myRId) -- calls the track entry form for TrackId: myTId
//                if myTId = 0 then it will open a blank form for upload.
//                myRId is the Recording primary key.
//--------------------------------------------------------------------------------
function showTrackEntry(myTId, myRId) {
  if (myTId === null || myRId === null) { 
      return false; 
  } else {
      clearDiv("recordView");
      ohp.myTrackId = myTId;
      ohp.myRecordId = myRId;
      var myURL = "TracksEntryForm.php?TId=" + myTId + "&RId=" + myRId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//-----------------------------------------------------------------------------------------------------------
//  function uploadDone(name, myRId) - Handles the response text from WriteTracksMetadata.php script, which
//                                     is passed in the iframe "name".  The response string will indicate
//                                     a successful upload, or it will contain an error message.
//                                     The "myRId" value is the RecordingId number used to retreive the
//                                     Recording metadata view, which will refresh the tracks list.
//-----------------------------------------------------------------------------------------------------------
  function uploadDone(name, myRId) {
    var frame = frames[name];
    if (frame) {
        var ret = frame.document.getElementsByTagName("body")[0].innerHTML;
        if (ret.length) {
            alert(ret);
            frame.document.getElementsByTagName("body")[0].innerHTML = "";
            showRecordView(myRId);
        }
    }
  }

//------------------------------------------------------------------------------------------------------
// showTranscriptsEntry(myTId, myUser, myAudio, myInit, myStartTime) --
//                Calls the transcript entry form for myTId and myUser, and loads myAudio in player;
//                if any variable is null an alert is posted, but nothing else.
//                myTId is the Track's primary key, and myUser is the User's primary key.
//                myInit is a boolean for whether the transcript has been initialized before; if
//                it has, open the TranscriptsEntryForm, if not, show the segmentation controls, which
//                create empty transcription records for the track based on silence gaps, and then
//                open the TranscriptsEntryForm for the blank records.
//                The value of myStartTime is used to trick the player's timeline into showing the
//                playback time as if it had started from the beginning of the whole recording rather
//                than the actual track.  It gets calculated in the loop that shows the actual tracks.
//                The drawRegionsFromTEF() function reads start and stop times from the transcript
//                entry form and draws regions on the waveform.
//------------------------------------------------------------------------------------------------------
function showTranscriptsEntry(myTId, myUser, myAudio, myInit, myStartTime) {
  if (myTId == null || myTId == 0 || myUser == null || myUser == 0 || myAudio == null) { 
      alert ("Track or user is null -- cannot open transcription form.");
      return false; 
  } else {
      // Determine whether to load a new audio file...
      if (ohp.myAudioFile !== myAudio) {
          // If there is already an audio file loaded, unload it...
          if (ohp.myAudioFile !== "none") { wsUnload(); }
          
          //  Load the new Audio file...
          ohp.startTime = myStartTime;
          showPlayer();
          ohp.myAudioFile = myAudio;
          wavesurfer.load(myAudio);
          wavesurfer.clearRegions();

      }
      setTimeout(function(){wavesurfer.play(0,.01);}, 500);
      ohp.myTrackId = myTId;
      ohp.myUserId = myUser;

      var myURL;
      if (myInit == 1) {
          myURL = "TranscriptsEntryForm.php?TId=" + myTId + "&UId=" + myUser + "&sT=" + myStartTime;
          processAjax (myURL, "annotations");
          setTimeout(function(){ drawRegionsFromTEF(); }, 3000);
          if (hideSegmentControls == true) { setBlockVis("segmentation", "none"); }
      } else {
          setBlockVis("segmentation", "inline-block");
      }
      setBlockVis("annotations", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// initTranscription() -- Collects the regions from wavesurfer.regions, formats
//                        URL, and passes parameters on to PHP to create the
//                        transcription records for the track and user.
//--------------------------------------------------------------------------------
function initTranscription() {
  var theRegions = saveRegions();
  allowRegionSplit = false;
  var myURL = "CreateTranscription.php?TId=" + ohp.myTrackId + "&UId=" + ohp.myUserId + "&Seg=" + theRegions + "&sT=" + ohp.startTime;
  if (testMode == true) { alert(myURL); } else { processAjax (myURL, "annotations"); }
}

//--------------------------------------------------------------------------------
// showUserView(myId) -- calls the user's admin record view for user: myId
//                       and displays the results in the recordView div
//--------------------------------------------------------------------------------
function showUserView(myId) {
  if (myId < 1) { 
      return false; 
  } else {
      clearDiv("recordView");
      var myURL = "UsersRecordView.php?U=" + myId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// showUserProfile(myId) -- calls the user's profile view for user: myId
//                       and displays the results in the recordView div
//--------------------------------------------------------------------------------
function showUserProfile(myId) {
  if (myId < 1) { 
      return false; 
  } else {
      clearDiv("recordView");
      var myURL = "UsersProfileView.php?U=" + myId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// showUserEntry(myId) -- calls the Admin's user entry form for userId: myId
//                        if myId = 0 then it will open a blank form
//--------------------------------------------------------------------------------
function showUserEntry(myId) {
  if (myId === null) { 
      return false; 
  } else {
      clearDiv("recordView");
      var myURL = "AdminUsersEntryForm.php?U=" + myId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      showRecaptcha();
      document.getElementById("UserName").focus();
      return true;
  }
}

//--------------------------------------------------------------------------------
// showRegistration(myId) -- calls the user entry form for userId: myId
//                           if myId = 0 then it will open a blank form
//--------------------------------------------------------------------------------
function showRegistration(myId) {
  if (myId === null) { 
      return false; 
  } else {
      clearDiv("recordView");
      var myURL = "RegistrationForm.php?U=" + myId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      showRecaptcha();
      return true;
  }
}

//--------------------------------------------------------------------------------
// forgotPassword(myUserName) --   calls the PHP script to send the password
//                                 reminder to myUserName. If myUserName = null then it 
//                                 alerts the user and stops.
//--------------------------------------------------------------------------------
function forgotPassword(myUserName) {
  if (myUserName) { 
      var myURL = "PasswordHintEmailer.php?U=" + myUserName;
      processAjax (myURL, "logIn");
      setBlockVis("logIn", "block");
      return true;
  } else {
      alert("Please enter your E-mail address and click the link again.");
      return false; 
  }
}

//--------------------------------------------------------------------------------
// showPWReset(myId) -- calls the password reset form for userId: myId
//                      only allowed for exisiting users where myId > 0.
//--------------------------------------------------------------------------------
function showPWReset(myId) {
  if (myId === null || myId < 1) { 
      return false; 
  } else {
      var myURL = "PasswordResetForm.php?U=" + myId;
      processAjax (myURL, "logInMsg");
      setBlockVis("logInMsg", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------------
// showRecordKeyword(myKw) --        Calls the keyword search for the keyword entered or
//                                   clicked & displays results in the "keyWord" div.
//                                   The query will search all recordings, annotations, 
//                                   and transcriptions for the search term myKw.  
//                                   The user can restrict the search by unchecking any
//                                   of the three check boxes on the search form and
//                                   either pressing the Submit button (for typed search 
//                                   terms) or clicking the keyword link (in the 
//                                   recording metadata or annotations list). The routine 
//                                   passes the query term, and a 0 or 1 to the variables 
//                                   r, a, & t and in order to restrict the query to one 
//                                   or more of the sources.
//--------------------------------------------------------------------------------------
function showRecordKeyword(myKw) {
  myKw = myKw.trim();
  if (myKw == "") { 
      alert("Please enter a search term in the keyword search form.");
      return false; 
  } else {
      // Construct the destination parameters to send to the query...a 1 means it will be shown
      var r=1;  // Recording metadata included...
      var a=1;  // Annotations included...
      var t=1;  // Transcriptions included...
      if (document.getElementById('Rec').checked !== true) { r = 0; }
      if (document.getElementById('Ann').checked !== true) { a = 0; }
      if (document.getElementById('Trn').checked !== true) { t = 0; }

      if (r + a + t == 0) {
          alert("Please check one of the boxes for the keyword query.");
          return false;
      }

      var myURL = "RecordingsKeyword.php?kw=" + myKw + "&r=" + r + "&a=" + a + "&t=" + t;
      processAjax (myURL, "keyWord");
      setBlockVis("keyWord", "block");
      return true;
  }
}

//-----------------------------------------------------------------------------------------------------------
// showRecordView(myRId, myTId) -- calls the recording record view for recordingId: myRId and trackId: myTId
//                          and displays the results in the recordView div
//-----------------------------------------------------------------------------------------------------------
function showRecordView(myRId, myTId) {
  myTId = myTId || 0;
  if (myRId < 1) { 
      return false; 
  } else {
      clearDiv("recordView");
      ohp.myRecordingId = myRId;
      var myURL = "RecordingsRecordView.php?RId=" + myRId + "&TId=" + myTId;
      processAjax (myURL, "recordView");
      setBlockVis("recordView", "block");
      return true;
  }
}

//-----------------------------------------------------------------------------------------------------------
// delRecord(myBlock, myRec) -- Calls the CascadeDelete.php script for myRec, and places the results in
//                              myBlock.  The myRec variable takes the form of "C=n" for compilations,
//                              "R=n" for recordings, "T=n" for tracks, "A=n" for annotations, and "T=n&U=n"
//                              for Transcripts.  The myBlock variable is either "recordview", "columnLeft" or
//                              "annotations" depending on where we want to display the results.
//-----------------------------------------------------------------------------------------------------------
function delRecord(myBlock, myRec) {
  if (typeof(myBlock) == 'undefined' || myBlock == null || typeof(myRec) == 'undefined' || myRec == null) { 
      return false;
  } else {
      var x = confirm("Are you sure you want to delete this record?");
      if (x) {
          clearDiv(myBlock);
          if (myBlock == 'recordings') { clearDiv('recordView'); }
          var myURL = "CascadeDelete.php?" + myRec;
          // alert(myBlock + "\n" + myURL); 
          processAjax (myURL, myBlock);
          setBlockVis(myBlock, "block");
          return true;
      } else { return false;}
  }
}

//--------------------------------------------------------------------------------
// logOut() -- performs all the cleanup required on the browser for logging out
//--------------------------------------------------------------------------------
function logOut() {
  clearDiv("annotations");
  hidePlayer();
  clearDiv("recordView");
  clearDiv("keyWord");
  ohp.myRecordingId = 0;
  ohp.myTrackId = 0;
  ohp.myUserId = 0;
  ohp.myUserName = null;
  ohp.myTranscriptId = 0;
  ohp.myAudioFile = "none";
  ohp.myLength = 0;
  ohp.myStart = 0;
  ohp.myStop = 0;
  ohp.startTime = 0;
  ohp.canUpload = 0;
  ohp.isAdmin = 0;
  ohp.annotatesAll = 0;
  ohp.annotatesOwn = 0;
  processAjax ("Introduction.php", "columnLeft");
  processAjax("logout.php", "logIn");
}