<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  //Start session
  ob_start();
  session_start();
  $_SESSION['s_attempts'] = 0;

  // Assign session variables to local variables.
  $myId = $_SESSION['s_user_id'];
  $myEmail = $_SESSION['s_email'];
  $myName =  $_SESSION['s_username'];

  require_once("header.php");
?>
   <body onload="initOHP()">
    <div id='rowOne'>
      <div class='wrapper' style='background-color: white; height: 80px; line-height: 80px; margin-top: -20px'>
        <img src="images/BANNER6.bmp" style='margin-top: 0px'>
      </div>
    </div>
    <div id='topLinkBar'>
      <div class='wrapper' style='height: 20px; line-height: 20px;'>
        <div id='link_bar' style='margin-top:-20px;'>&nbsp;<?php echo $myHome; ?></div>
      </div>
    </div>
    <div class='wrapper' style="margin-top:1em">
      <?php echo $pwMessage; ?>
      <div id='columnRight'>
        <div id='logIn' style='text-align: center'>
          <div id='logInMsg'></div>
        </div>
        <div id='kwSearch'></div>
        <div id='keyWord'></div>
      </div>
      <div id='player'>
        <div id='waveform'>
          <div class='progress progress-striped active' id='progress-bar'>
            <div class='progress-bar progress-bar-info'></div>
          </div>
        </div>  <!-- End of waveform -->
        <div id='wave-timeline'></div>
        <div class='controls'>
          <button class='b1' onclick='wsUnload()'><i class='glyphicon glyphicon-eject'></i> Unload</button>
          <button class='b1' data-action='restart'><i class='glyphicon glyphicon-repeat'></i> Restart</button>
          <button class='b1' data-action='back'><i class='glyphicon glyphicon-step-backward'></i>Backward</button>
          <button class='b1' data-action='play'><i class='glyphicon glyphicon-play'></i>Play / <i class='glyphicon glyphicon-pause'></i>Pause</button>
          <button class='b1' data-action='forth'><i class='glyphicon glyphicon-step-forward'></i>Forward</button>
          <button class='b1' data-action='toggle-mute'><i class='glyphicon glyphicon-volume-off'></i>Toggle Mute</button>
          <div id="btnAnnotate">
               <button class='b2' data-action='green-mark'><i class='glyphicon glyphicon-tag'></i> Annotate</button>  
               <!--  Remove the comment markers from the next two lines to activate the stop annotation and clear marks buttons. -->
               <!--  <button class='b8' data-action='red-mark'><i class='glyphicon glyphicon-tag'></i> End Annotation</button>   -->
               <!--  <button class='b1' data-action='clear-marks'><i class='glyphicon glyphicon-remove-circle'></i> Clear Marks</button> -->
          </div><br>
          <div id="regions" style="display:block"></div>
          <div id="segmentation">
            <h5 class='center'>Audio File Segmentation Tools</h5>
            <p class='tiny'>These segmentation tools are used to create sound blocks for transcription. 
              Press the "Segment Audio" button to create initial segments based on silence in the audio. 
              Adjust the values with the sliders and press the "Segment" button again to see the effect. 
              Reducing the slider values creates fewer segments. 
              You can double-click a region to split it at the cursor location.
              You can use the mouse cursor to resize segments by clicking their edges and dragging to the left or right.
              When you are satisfied with the segmentation, press the "Transcribe" button; 
              the application will create transcript blocks for the segments.</p>
            <button class='b1' onClick='findRegions()'><i class='glyphicon glyphicon-stats'></i> Segment Audio</button> &nbsp; 
            <!-- slider controls to affect segmentation: -->
            Minimum volume: <input type='range' min='0.001' max='0.010' value='0.006' step='0.0005' onchange="showValue('minVol', this.value); minValue = this.value" /> <span id='minVol'>0.006</span> &nbsp; 
            Minimum duration: <input type='range' min='0.1' max='0.5' value='0.3' step='0.005' onchange="showValue('minDur', this.value); minDuration = this.value" /> <span id='minDur'>0.3</span> &nbsp;
            <button class='b1' onClick='initTranscription()'><i class='glyphicon glyphicon-pencil'></i> Transcribe</button> &nbsp; 
          </div><br>
         </div>  <!-- controls --><br>
      </div>  <!-- player -->
      <div id='annotations'></div>
      <div id='recordView'>
        <?php
          // This allows a direct link into a recording metadata view,
          // so we can create external links to the recordings that will
          // load everything that's needed to play and annotate.  We just
          // call the website with "?RId=1" appended to the URL to get
          // recording #1.
          if (isset($_GET['RId'])) :
              require_once("RecordingsRecordView.php"); 
         endif;
        ?>
      </div>
      <div id='columnLeft'></div>
      <div id='footer'></div>
    </div>  <!-- wrapper -->
  </body>
</html>