//<![CDATA[
// default.js -- Standard javascript routines...

//------------------------------------------------------------------------------------
function detect(){
  if(screen.width<1280){
     alert("This page is best viewed with a screen width of 1280 or greater.  Your current screen width is "+screen.width+".  If possible please change your resolution.")
     }
}

//------------------------------------------------------------------------------------
// newWindow(myURL) -- Open a no-frills window for data entry
//------------------------------------------------------------------------------------
  function newWindow(myURL) {
// alert(myURL);
   var mywindow = window.open (myURL,"mywindow","location=0,status=0,toolbar=0,menubar=0,directories=0,resizeable=1,scrollbars=1,width=640,height=480");
  } 


//------------------------------------------------------------------------------------
// setVis(myId) -- turns on the visibility of any element by id
//------------------------------------------------------------------------------------
  function setVis(myId) {
    var e = document.getElementById(myId);
    e.style.display = 'block';
  }

//------------------------------------------------------------------------------------
// setBlockVis(myId, myStyle) -- switches the display property of any element by id
//------------------------------------------------------------------------------------
  function setBlockVis(myId, myStyle) {
    // alert(myId + " -> " + myStyle);
    var e = document.getElementById(myId);
    switch(myStyle) {
      case "block":
        e.style.display = 'block';
        break;
      case "inline-block":
        e.style.display = 'inline-block';
        break;
      case "inline":
        e.style.display = 'inline';
        break;
      case "table-cell":
        e.style.display = 'table-cell';
        break;
      case "none":
        e.style.display = 'none';
    }
  }

//------------------------------------------------------------------------------------
// blockVis(myId) -- turns off the visibility of any element by id
//------------------------------------------------------------------------------------
  function blockVis(myId) {
    var e = document.getElementById(myId);
    e.style.display = 'none';
  }

//------------------------------------------------------------------------------------
// toggleVis(myId) -- toggles the visibility of any element by id
//------------------------------------------------------------------------------------
  function toggleVis(myId) {
    var e = document.getElementById(myId);
    if(e.style.display == 'block')
       e.style.display = 'none';
    else
       e.style.display = 'block';
  }


//--------------------------------------------------------------------------------
// clearDiv(myDiv) -- clears the requested div and turns it's display to none
//--------------------------------------------------------------------------------
function clearDiv(myDiv) {
  var e = document.getElementById(myDiv);
  e.innerHTML = "";
  e.style.display = 'none';
}

//--------------------------------------------------------------------------------
// showValue(myLoc, myContent) -- puts myContent into myLoc
//--------------------------------------------------------------------------------
function showValue(myLoc, myContent) {
  document.getElementById(myLoc).innerHTML = myContent;
}

//--------------------------------------------------------------------------------
// dump(arr, level) -- essentially duplicates PHP's print_r function, where arr
//                     is the array to dump, and level is the optional level to
//                     begin the dump.
//--------------------------------------------------------------------------------
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}


//]]>