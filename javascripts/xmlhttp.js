//------------------------------------------------------------------------------------
// processAjax - Processes Ajax calls.
//------------------------------------------------------------------------------------
function processAjax (url, resultID) {
  // Create xmlhttp request object
  var xmlhttp=false;
  try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } 
  catch (e) {
             try { xmlhtttp = new ActiveXObject("Microsoft.XMLHTTP"); } 
             catch (E) { xmlhttp = false; }
            }

  if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp = new XMLHttpRequest(); }

  if (xmlhttp) {
      var obj;
      xmlhttp.open("GET", url, true);
      xmlhttp.onreadystatechange=function() {
         if (xmlhttp.readyState==4) {
             obj = document.getElementById(resultID);
             obj.innerHTML = xmlhttp.responseText;
             var resp = xmlhttp.responseText;
             // Check to see if any Javascript was returned, and execute it if there was...
             parseScript(resp, url);
         }
      }
      xmlhttp.send(""); 
      return 0;
    //  return true;
  }
}

//-----------------------------------------------------------------------------------------------
// Message handler
//  - Sends message to script and waits for completion
//  - On completion, updates page with script output
//-----------------------------------------------------------------------------------------------
// url = url to call server-side script
// resultID = ID of DIV or SPAN element to receive the output
//-----------------------------------------------------------------------------------------------
function handleMessages (url, resultID) {
  // Create xmlhttp request object
  var xmlhttp=false;
  try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } 
  catch (e) {
              try { xmlhtttp = new ActiveXObject("Microsoft.XMLHTTP"); } 
              catch (E) { xmlhttp = false; }
            }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp = new XMLHttpRequest(); }
  if (xmlhttp) {
      var obj;
      xmlhttp.open("GET", url, true);
      xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4) {
            obj = document.getElementById(resultID);
            obj.innerHTML = xmlhttp.responseText;
        }
      }
      xmlhttp.send(""); 
      return 0;
  }
}

//-------------------------------------------------------------------------------------
function handleForm (url, params, resultID) 
  {
   // Create xmlhttp request object
   var xmlhttp=false;
   try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } 
   catch (e) {
               try { xmlhtttp = new ActiveXObject("Microsoft.XMLHTTP"); } 
               catch (E) { xmlhttp = false; }
             }
   if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp = new XMLHttpRequest(); }
   if (xmlhttp) 
      {
  //     var obj;
       xmlhttp.open("POST", url, true);
       //Send the proper header information along with the request
       xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
       xmlhttp.setRequestHeader("Content-length", params.length);
       xmlhttp.setRequestHeader("Connection", "close");
       xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
       if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
         {
  //             obj = document.getElementById(resultID);
          resultID.innerHTML = xmlhttp.responseText;
          // calls the parseScript() function, with the response from PHP as argument
          var resp = xmlhttp.responseText;
          parseScript(resp, url);
         }
       } // end inline function
       xmlhttp.send(params);
      } // end if (xmlhttp)
  } // end function


//-------------------------------------------------------------------------------------
// Function to create an Array to contain JS code of every <script> tag in parameter
// then apply the eval() to execute the code in every script collected
//-------------------------------------------------------------------------------------
function parseScript(strcode, myUrl) {
  var scripts = new Array();         // Array which will store the script's code
  // Strip out tags
  while(strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
    var s = strcode.indexOf("<script");
    var s_e = strcode.indexOf(">", s);
    var e = strcode.indexOf("</script", s);
    var e_e = strcode.indexOf(">", e);
    
    // Add to scripts array
    scripts.push(strcode.substring(s_e+1, e));
    // Strip from strcode
    strcode = strcode.substring(0, s) + strcode.substring(e_e+1);
  }
  
  // Loop through every script collected and eval it
  for(var i=0; i<scripts.length; i++) {
    try {
      eval(scripts[i].innerText);
      // alert(scripts[i]);
    }
    catch(ex) {
      // do what you want here when a script fails
      // alert ("There was an error in the script generated by " + myUrl);
    }
  }
}

//------------------------------------------------------------------------------------
//Function to create an XMLHttp Object.
//------------------------------------------------------------------------------------
function getXmlhttp ()
  {//Create a boolean variable to check for a valid microsoft active X instance.
   var xmlhttp = false;
   //Check if we are using internet explorer.
   try  //If the javascript version is greater than 5.
        { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } 
   catch (e) 
         { //If not, then use the older active x object.
           try  //If we are using internet explorer.
               { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); } 
           catch (e)  //Else we must be using a non-internet explorer browser. 
                 { xmlhttp = false; }
	 }
   //If we are using a non-internet explorer browser, create a javascript instance of the object.
   if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {	xmlhttp = new XMLHttpRequest();	}
   return xmlhttp;
  }
	
//------------------------------------------------------------------------------------
// function getFormValues (myForm) -- serializes form values
//          where: myForm is the form to serialize.
//------------------------------------------------------------------------------------
function getFormValues (myForm){
  var myParms = "";
  for (var i = 0, d, v; i < myForm.elements.length; i++) {
       d = myForm.elements[i];
       if (d.name && d.value) {
           v = (d.type == "checkbox" || d.type == "radio" ? (d.checked ? d.value : '') : d.value);
           if (v) myParms += d.name + "=" + escape(v) + "&";
       }
  }
  myParms = myParms.substr(0,myParms.length-1);
  // alert("myParms = " + myParms);
  return myParms;
}

//------------------------------------------------------------------------------------
// function submitForm (myForm, myScript, theDiv) -- submits a form via Ajax
//          where : myForm = the form,
//                  myScript = the destination script on the server,
//                  myDiv = the name of the div on the current page for the results.
//------------------------------------------------------------------------------------
function submitForm (myForm, myScript, theDiv){
  // alert("myForm = " + myForm + "\nmyScript = " + myScript + "\ntheDiv = " + theDiv);
  var myParms = getFormValues(myForm);
  var myDiv = document.getElementById(theDiv);
  handleForm(myScript, myParms, myDiv);
}