// Silence and Segmentation Controls Parameters
   var minValue = 0.006;
   var minSeconds = 0.30;
   var myRegions = [];
   var allowRegionSplit = false;

   var testMode = false;
   var hideSegmentControls = !testMode;

//------------------------------------------------------------------------------------------
// loadAudio(myAudio, myTId, myDetail, myStartTime) -- loads an mp3 file into the wavesurfer
//                                        window and loads annotations  or transcriptions 
//                                        into the annotations div, depending on the value
//                                        of myDetail (0 = annotations, 1 = transcriptions).
//                                        The value of myStartTime determines what the time
//                                        line numbers start at, and doesn't affect the 
//                                        time annotation functions, just the display.
//------------------------------------------------------------------------------------------
function loadAudio(myAudio, myTId, myDetail, myStartTime) {
  if (myAudio === null || myTId === null) {
      return false;
  } else {

      // Determine whether to load a new audio file...
      if (ohp.myAudioFile !== myAudio) {
          // If there is already an audio file loaded, unload it...
          if (ohp.myAudioFile !== "none") { 
              wsUnload();
              document.getElementById('wave-timeline').innerHTML = "";
          }
          
          //  Load the new Audio file...
          //  To change the listed start time, use "ohp.startTime = nnn;", where nnn = number of seconds.
          //  The start time of the track should be calculated from the start of the first track, which is
          //  done in the FetchTracks($myRecordingId) function in LookUpFunctions.php.

          ohp.startTime = myStartTime;
          showPlayer();
          ohp.myAudioFile = myAudio;
          wavesurfer.load(myAudio);
      }

      pullGlobalValuesFromMenu();

      // Determine whether to show the Annotation buttons, based on the state of four different variables.
      // If any of them is true, the user can annotate the recording, if they are in the annotations page (where myDetail = 0).
      if (myDetail == 0) {
          // The first set of conditions that enable the user to annotate is based on user permissions in the user record.
          var sumOfUserPermissions = ohp.isAdmin + ohp.annotatesAll + ohp.annotatesOwn;
          if (ohp.myUserId > 0 && sumOfUserPermissions > 0) {
              setBlockVis("btnAnnotate", "inline");
          } else {
              setBlockVis("btnAnnotate", "none");
          }
          // The second set of conditions that enables the user to annotate is based on user permissions in the compilations2user record,
          // It is only applicable if the system is using CompilationsUsers (determined by the $UseCompilationsUsers variable in the
          // "settings.php" file, and passed to hidden fields in the menu by login.php and login-test.php, then pulled from the menu
          // by the call to pullGlobalValuesFromMenu().

          if(ohp.useCompilationsUsers == 1) {
             var sumOfUserCompPermissions = compilationPermissions.canAnnotate + compilationPermissions.isAdmin;
             if (ohp.myUserId > 0 && sumOfUserCompPermissions > 0) {
                 setBlockVis("btnAnnotate", "inline");
             } else {
                 setBlockVis("btnAnnotate", "none");
             }
          }
          loadAnnotations(myTId);
      } else { 
          loadTranscripts(myTId);
      } 
      return true;
  }
}

//-------------------------------------------------------------------------------------------------
// controlLoader(myTrackNum, myDetail) -- Controls the loading of audio tracks by taking the value
//                                        of myTrackNum and using it to pull vars from the form
//                                        array where the tracks are listed for a recording.  This
//                                        is used in conjunction with the 'finish' event and the
//                                        'autoLoop' javascript value to format and call the 
//                                        loadAudio function, which handles the actual loading of
//                                        the audio file and the annotations list.  The 'myDetail'
//                                        value is either a 0 or a 1.  If it's a 0, then the
//                                        annotations get loaded with the audio; if it's a 1 then
//                                        the transcripts get loaded.
//-------------------------------------------------------------------------------------------------
function controlLoader(myTrackNum, myDetail) {
  if (myTrackNum === null) { myTrackNum = 0; }
  if (myDetail === null) { myDetail = 0; }

  currentTrack = myTrackNum;
  var i = myTrackNum * 3;
  var str = document.myTracks.elements[i].value;
  myAudio = str.toString();
  myTId = Number(document.myTracks.elements[i+1].value);
  myStartTime = Number(document.myTracks.elements[i+2].value);
  myDetail = Number(myDetail);
  wavesurfer.clearRegions();
  if (hideSegmentControls == true) { setBlockVis("segmentation", "none"); }
  loadAudio(myAudio, myTId, myDetail, myStartTime);
}

//-------------------------------------------------------------------------------------------------
// findRegions() -- Controls the detection and drawing of audio regions.
//-------------------------------------------------------------------------------------------------
function findRegions() {
  wavesurfer.clearRegions();
  var peaks = wavesurfer.backend.getPeaks(512);
  var duration = wavesurfer.getDuration();
  var wsRegions = extractRegions(peaks, duration);
  drawRegions(wsRegions);
  allowRegionSplit = true;
}

//-------------------------------------------------------------------------------------------------
// Draw regions from extractRegions function.
//-------------------------------------------------------------------------------------------------
function drawRegions(regions) {
  regions.forEach(function (region) {
  region.drag = false;
  region.resize = true;
  region.color = randomColor(0.2);
  wavesurfer.addRegion(region);
  });
}

//-------------------------------------------------------------------------------------------------
// Draw regions from TranscriptEntryForm.  Once transcript blocks have been created they are filled
//                                         in via the TranscriptsEntryForm, which is displayed via
//                                         an AJAX call.  Three seconds later, this function is
//                                         called, which reads the number of regions, and the
//                                         start and end times for each one, from the form, and 
//                                         then calls the wavesurfer.addRegion method.  SecondsIn
//                                         and SecondsOut are the start and end times.
//-------------------------------------------------------------------------------------------------
function drawRegionsFromTEF() {
  var numRegions = document.forms['TranscriptEntryForm'].elements['NumRegions'].value;
  var startTimes = document.forms['TranscriptEntryForm'].elements['SecondsIn'];
  var stopTimes = document.forms['TranscriptEntryForm'].elements['SecondsOut'];
  var i;
  for (i = 0; i < numRegions; i++) {
       var region = {};
       region.drag = false;
       region.resize = false;
       region.start = startTimes[i].value;
       region.end = stopTimes[i].value;
       region.color = randomColor(0.2);
       wavesurfer.addRegion(region);
  }
}

//-------------------------------------------------------------------------------------------------
// Save regions to myRegions array before sending to PHP, in case the regions were edited by hand.
// The function also turns the resize flags to false for the regions already drawn on the waveform
// so they can't be modified once the information is sent to the PHP script, which writes the
// MySQL transcription records based on the region start and end times.
//-------------------------------------------------------------------------------------------------
function saveRegions() {
  var myRegions = [];
  Object.keys(wavesurfer.regions.list).map(function (id) {
                var region = wavesurfer.regions.list[id];
                region.resize = false;
                myRegions.push(id, region.start, region.end);
                // This part updates the regions, turning the resize flags to false, so the region
                // can not be modified after the commit key is pressed. 
                //region.update({resize : false});
              });
  return JSON.stringify(myRegions);
}

//-------------------------------------------------------------------------------------------------
// Extract regions separated by silence.
//-------------------------------------------------------------------------------------------------
function extractRegions(peaks, duration) {
    var length = peaks.length;
    var coef = duration / length;
    var minLen = minSeconds / coef;

    // Gather silence indeces
    var silences = [];
    Array.prototype.forEach.call(peaks, function (val, index) {
        if (val < minValue) {
            silences.push(index);
        }
    });

    // Cluster silence values
    var clusters = [];
    silences.forEach(function (val, index) {
        if (clusters.length && val == silences[index - 1] + 1) {
            clusters[clusters.length - 1].push(val);
        } else {
            clusters.push([ val ]);
        }
    });

    // Filter silence clusters by minimum length
    var fClusters = clusters.filter(function (cluster) {
        return cluster.length >= minLen;
    });

    // Create regions on the edges of silences
    var regions = fClusters.map(function (cluster, index) {
        var next = fClusters[index + 1];
        return {
            start: cluster[cluster.length - 1],
            end: (next ? next[0] : length - 1)
        };
    });

    // Add an initial region if the audio doesn't start with silence
    var firstCluster = fClusters[0];
    if (firstCluster && firstCluster[0] != 0) {
        regions.unshift({
            start: 0,
            end: firstCluster[firstCluster.length - 1]
        });
    }

    // Filter regions by minimum length
    var fRegions = regions.filter(function (reg) {
        return reg.end - reg.start >= minLen;
    });

    // Return time-based regions
    return fRegions.map(function (reg) {
        return {
            start: Math.round(reg.start * coef * 10) / 10,
            end: Math.round(reg.end * coef * 10) / 10
        };
    });
}

//-------------------------------------------------------------------------------------
// Random RGBA color.
//-------------------------------------------------------------------------------------
function randomColor(alpha) {
    return 'rgba(' + [
        ~~(Math.random() * 255),
        ~~(Math.random() * 255),
        ~~(Math.random() * 255),
        alpha || 1
    ] + ')';

}

//-------------------------------------------------------------------------------------
// setAutoAdvance(myVal) -- Turns on the auto looping feature by setting the global
//                          value 'autoLoop' to equal the number of tracks. If there is
//                          only one track it will loop continuously, but if there are
//                          multiple tracks, as one ends the next one will be loaded
//                          and played automatically, with the last track looping back
//                          to the first track.  This will continue until the user sets
//                          the autoLoop value to 0 by clicking the No option.
//-------------------------------------------------------------------------------------
function setAutoAdvance(myVal) {
  autoLoop = myVal;
}

//-------------------------------------------------------------------------------------
// dropRedFlag(myStopTime) -- Gets the current time of the audio track and moves it 
//                            to the SecondsOut field on the Annotations entry form.
//                            Currently this isn't used.
//-------------------------------------------------------------------------------------
function dropRedFlag(myStopTime) {
  if (myStopTime === null) {
      return false;
  } else {
      try { document.form.SecondsOut.value = myStopTime; }
      catch(err) { } 
  }
}

//-------------------------------------------------------------------------------------
// pullGlobalValuesFromMenu() -- Pulls the global values from the menu, so they can
//                               be pushed into the ohp.variable array.
//-------------------------------------------------------------------------------------
function pullGlobalValuesFromMenu() {
  //ohp.myRecordingId = document.getElementById("myRecordingId").value;
  //ohp.myTrackId = document.getElementById("myTrackId").value;
  //ohp.myTranscriptId = document.getElementById("myTranscriptId").value;
  //ohp.myAudioFile = document.getElementById("myAudioFile").value;
  //ohp.myStart = document.getElementById("myStart").value;
  //ohp.myStop = document.getElementById("myStop").value;
  //ohp.startTime = document.getElementById("myStartTime").value;
  ohp.myUserId = document.getElementById("myUserId").value;
  ohp.myUserName = document.getElementById("myUserName").value;
  ohp.canUpload = document.getElementById("canUpload").value;
  ohp.isAdmin = document.getElementById("isAdmin").value;
  ohp.annotatesAll = document.getElementById("annotatesAll").value;
  ohp.annotatesOwn = document.getElementById("annotatesOwn").value;
  ohp.defaultPrefixCode = document.getElementById("defaultPrefixCode").value;
  ohp.restrictQueriesToThisInstance = document.getElementById("restrictQueriesToThisInstance").value;
  ohp.useCompilationsUsers = document.getElementById("useCompilationsUsers").value;
}

//-------------------------------------------------------------------------------------
// wsUnload() -- unloads the current audio file and its supporting variables and divs.
//-------------------------------------------------------------------------------------
function wsUnload() { 
  ohp.myAudioFile = "none";
  ohp.myLength = 0;
  ohp.myStart = 0;
  ohp.myStop = 0;
  wavesurfer.stop();
  wavesurfer.empty();
  document.getElementById('wave-timeline').innerHTML = "";
  document.getElementById('annotations').innerHTML = "";
  hidePlayer();
}

//-----------------------------------------------------------------------------------
// jumpTo(myTimeStamp, myTsEnd) -- Plays the loaded audio file from myTimeStamp.
//                                 Optional myTsEnd sets the end point; if it's null
//                                 it will play to the end of the audio clip.
//-----------------------------------------------------------------------------------
function jumpTo(myTimeStamp, myTsEnd) {
  if (myTimeStamp === null) {
      return false;
  } else {
      // This resets to the start of the audio file.
      wavesurfer.stop();
      // Now wait a bit and start from the point indicated.
      // End either at the end of the audio file or at the passed-in value of myTsEnd.
      if (myTsEnd === null) { setTimeout(function(){wavesurfer.play(myTimeStamp); }, 500); }
      else { setTimeout(function(){wavesurfer.play(myTimeStamp, myTsEnd); }, 500); }
  }
}

//-----------------------------------------------------------------------------------
// playBlock(myTimeStamp, myLength) -- plays the loaded audio file from myTimeStamp 
//                                     to (myTimeStamp + myLength)
//-----------------------------------------------------------------------------------
function playBlock(myTimeStamp, myLength) {
  if (myTimeStamp === null || myLength === null) {
      return false;
  } else {
      var myEnd = myTimeStamp + myLength;
      // This resets to the start of the audio file.
      wavesurfer.stop();
      //  Now wait a bit and start from the point indicated.
      setTimeout(function(){wavesurfer.play(myTimeStamp, myEnd); }, 500);
  }
}

//--------------------------------------------------------------------------------
// loadAnnotations(myTId) -- loads annotations into the annotations block
//--------------------------------------------------------------------------------
function loadAnnotations(myTId) {
  if (myTId === null) {
      return false; 
  } else {
      ohp.myTrackId = myTId;
      var myURL = "AnnotationLister.php?TId=" + myTId + "&sT=" + ohp.startTime;
      processAjax (myURL, "annotations");
      setBlockVis("annotations", "block");
      fetchAnnotationMarkers(myTId);
      return true;
  }
}

//--------------------------------------------------------------------------------
// loadTranscripts(myTId) -- loads transcripts into the annotations block
//--------------------------------------------------------------------------------
function loadTranscripts(myTId) {
  if (myTId === null) {
      return false; 
  } else {
      ohp.myTrackId = myTId;
      var myURL = "TranscriptsLister.php?TId=" + myTId;
      processAjax (myURL, "annotations");
      setBlockVis("annotations", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// fetchAnnotationMarkers(myTId) -- Handles asynchronous fetch of region markers 
//                                  for annotations.
//--------------------------------------------------------------------------------
function fetchAnnotationMarkers(myTId) {
  if (myTId === null) {
      return false; 
  } else {
      clearDiv("regions");
      var myURL = "AnnotationsRegionFetch.php?TId=" + myTId;
      processAjax (myURL, "regions");
//      setBlockVis("regions", "none");
      setTimeout(function(){addAnnotationMarkers(); }, 1000);
  }
}

//--------------------------------------------------------------------------------
// addAnnotationMarkers() -- Reads annotation start times from "regions" div and
//                           adds a marker for each to the waveform.
//                           The marker string is in this format: "id,start;..."
//--------------------------------------------------------------------------------
function addAnnotationMarkers() {
  var strMarkers = document.getElementById("regions").innerHTML;
  if (strMarkers.length == 0) {
      return false;
  } else {
      wavesurfer.clearRegions();
      // There is an array string to be parsed into markers.
      var myMarkers = strMarkers.split(";");
      for (i = 0; i < myMarkers.length; i++) {
           var marker = myMarkers[i].split(",");
           wavesurfer.addRegion({id: 'marker[0]', start: marker[1], loop: false, drag: false, resize: false, color: 'rgba(0,170,0,1)'});
      } 
  }
}

//--------------------------------------------------------------------------------
// addAnnotation(myTimeStamp, myTId) -- Opens the Annotation Entry form
//                                      myTimeStamp = the time on the player when
//                                      the Annotate button was pushed; myTId =
//                                      the TrackId, which is a foreign key in
//                                      the annotations table.
//--------------------------------------------------------------------------------
function addAnnotation(myTimeStamp, myTId) {
  if (myTimeStamp === null || myTId === null) { 
      return false; 
  } else {
      var myURL = "AnnotationEntryForm.php?TS=" + myTimeStamp + "&TId=" + myTId;
      processAjax (myURL, "annotations");
      setBlockVis("annotations", "block");
      return true;
  }
}

//--------------------------------------------------------------------------------
// editAnnotation(myTagId) -- Opens the Annotation Entry form for editing
//--------------------------------------------------------------------------------
function editAnnotation(myTagId) {
  if (myTagId === null) { 
      return false; 
  } else {
      var myURL = "AnnotationEntryForm.php?AnnotationId=" + myTagId;
      processAjax (myURL, "annotations");
      setBlockVis("annotations", "block");
      return true;
  }
}