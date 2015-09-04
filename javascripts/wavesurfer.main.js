'use strict';

// Create an instance
var wavesurfer = Object.create(WaveSurfer);
var timeline = Object.create(WaveSurfer.Timeline);

// Init & load audio file
document.addEventListener('DOMContentLoaded', function () {
    var options = {
        container     : document.querySelector('#waveform'),
        waveColor     : '#FAC729',
        progressColor : '#740632',
        loaderColor   : '#740632',
        cursorColor   : 'navy',
        markerWidth   : 2,
        pixelRatio    : 1
    };

    if (location.search.match('scroll')) {
        options.minPxPerSec = 50;
        options.scrollParent = true;
    }

    if (location.search.match('normalize')) {
        options.normalize = true;
    }

    /* Progress bar */
    (function () {
        var progressDiv = document.querySelector('#progress-bar');
        var progressBar = progressDiv.querySelector('.progress-bar');

        var showProgress = function (percent) {
            progressDiv.style.display = 'block';
            progressBar.style.width = percent + '%';
        };

        var hideProgress = function () {
            progressDiv.style.display = 'none';
        };

        wavesurfer.on('loading', showProgress);
        wavesurfer.on('ready', hideProgress);
        wavesurfer.on('destroy', hideProgress);
        wavesurfer.on('error', hideProgress);
    }());

    // Init
    wavesurfer.init(options);
    // Load audio from URL
    // wavesurfer.load('tracks/AegeanSea.mp3');
});

   // Play at once when ready
   // Won't work on iOS until you touch the page
   wavesurfer.on('ready', function () { 
     ohp.myStart = 0;
     timeline.init({
       wavesurfer: wavesurfer,
       container: "#wave-timeline"
     });
     wavesurfer.play();
   });


  // Bind buttons and keypresses
  (function () {
    var eventHandlers = {
        'restart': function () {
            wavesurfer.stop();
            wavesurfer.play();
        },

        'play': function () {
            wavesurfer.playPause();
        },

        'green-mark': function () {
            wavesurfer.pause();
            ohp.myStart = wavesurfer.getCurrentTime();
            wavesurfer.clearRegions();
            wavesurfer.addRegion({id: '1', start: ohp.myStart, loop: false, drag: false, resize: false, color: 'rgba(0,128,0,1)'});
            // alert(ohp.myStart);
            addAnnotation(ohp.myStart,ohp.myTrackId);
        },

        'red-mark': function () {
            if (wavesurfer.getCurrentTime() > ohp.myStart) {
                wavesurfer.pause();
                ohp.myStop = wavesurfer.getCurrentTime();
                wavesurfer.addRegion({id: '2', start: ohp.myStop, loop: false, drag: false, resize: false, color: 'rgba(200,0,0,1)'});
                // alert(ohp.myStop);
                try {dropRedFlag(ohp.myStop);}
                catch (err) {}
            }
        },

        'back': function () {
            wavesurfer.skipBackward();
        },

        'forth': function () {
            wavesurfer.skipForward();
        },

        'toggle-mute': function () {
            wavesurfer.toggleMute();
        },

        'clear-marks': function() {
            wavesurfer.clearRegions();
            ohp.myStart = 0;
            ohp.myStop = 99999;
        }
    };

    document.addEventListener('click', function (e) {
        var action = e.target.dataset && e.target.dataset.action;
        if (action && action in eventHandlers) {
            eventHandlers[action](e);
        }
    });
}());

// Flash mark when it's played over
wavesurfer.on('mark', function (marker) {
    if (marker.timer) { return; }

    marker.timer = setTimeout(function () {
        var origColor = marker.color;
        marker.update({ color: 'yellow' });

        setTimeout(function () {
            marker.update({ color: origColor });
            delete marker.timer;
        }, 100);
    }, 100);
});

wavesurfer.on('error', function (err) {
    console.error(err);
});

wavesurfer.on('finish', function () {
    if (autoLoop > 0) {
        currentTrack = currentTrack + 1;
        if (currentTrack == autoLoop) { currentTrack = 0; }
        controlLoader(currentTrack);
    } else {     
     //   alert("Finished");
    }
});

wavesurfer.on('region-dblclick', function(region) {
    if (allowRegionSplit == true) {
        // Split the region in the middle...
        var splitPoint = wavesurfer.getCurrentTime();
        var endRegion = region.end;
        region.update({end: splitPoint});

        var newRegion = {};
        newRegion.drag = false;
        newRegion.resize = true;
        newRegion.start = splitPoint;
        newRegion.end = endRegion;
        newRegion.color = randomColor(0.2);
        wavesurfer.addRegion(newRegion);
    }
});

