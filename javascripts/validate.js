//----------------------------------------------------------------------------------------------------
function isEmpty(s)		// Check whether string s is empty.
{   return ((s == null) || (s.length == 0)) }

function isDigit (c)
{   return ((c >= "0") && (c <= "9")) }

//-------------------------------------------------------------------
// isInteger(value)
//   Returns true if value is a positive or negative integer
//-------------------------------------------------------------------
function isInteger(val){
      if(val==null){return false;}
      if (val.length==0){return false;}
      for (var i = 0; i < val.length; i++) {
            var ch = val.charAt(i)
            if (i == 0 && ch == "-") {
            continue;
            }
      if (ch < "0" || ch > "9") {
            return false;
      }
}
return true;
}
//-------------------------------------------------------------------
// isValidPositiveNumber(value)
//   Returns true if value is a positive number
//-------------------------------------------------------------------
function isValidPositiveNumber(val){
      if(val==null){return false;}
      if (val.length==0){return false;}
      var DecimalFound = false;
      for (var i = 0; i < val.length; i++) {
            var ch = val.charAt(i)
            if (ch == "." && !DecimalFound) {
                  DecimalFound = true;
                  continue;
            }
            if (ch < "0" || ch > "9") {
                  return false;
            }
      }
      return true;
}

//----------------------------------------------------------------------------------------------------
// isFloat (STRING s [, BOOLEAN emptyOK])
// 
// True if string s is an unsigned floating point (real) number. 
// Also returns true for unsigned integers. If you wish
// to distinguish between integers and floating point numbers,
// first call isInteger, like If (!isInteger (s, true)) && isFloat (s, true))
// Does not accept exponential notation.

function isFloat (s)
{   var i;
    var seenDecimalPoint = false;
    var decimalPointDelimiter = ".";
    if (isEmpty(s)) 
       if (isFloat.arguments.length == 1) return defaultEmptyOK;
       else return (isFloat.arguments[1] == true);
    if (s == decimalPointDelimiter) return false;

    // Search through string's characters one by one
    // until we find a non-numeric character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++)
    {   
        var c = s.charAt(i);	// Check that current character is number.
        if ((c == decimalPointDelimiter) && !seenDecimalPoint) seenDecimalPoint = true;
        else if (!isDigit(c)) return false;
    }
    return true;		// All characters are numbers.
}

//------------------------------------------------------------------------------------
function checkPositiveVal(val)
   {
    if(val.length > 0)
      {
    if (!isValidPositiveNumber(val)) {alert("Please enter a positive number.");}
      }
   }

//------------------------------------------------------------------------------------
function checkElev(Elevation)
   {
    if(Elevation.length > 0)
      {
       if (!isInteger(Elevation)) {alert("Please enter a whole number.");}
      }
   }

//------------------------------------------------------------------------------------
function checkIntRange(elem, Low, High) {
  var myVal = elem.value;   
  var themessage = "Please enter a whole number between " + Low + " and " + High + "."
    if(myVal.length > 0) { 
	if (!isInteger(myVal) || (myVal < Low || myVal > High)) { 
            alert(themessage);
            setTimeout("focusElement('" + elem.form.name + "', '" + elem.name + "')", 0); }
      }
   }

//------------------------------------------------------------------------------------
function checkFloatRange(elem, Low, High) {
  var myVal = elem.value;   
  var themessage = "Please enter a number between " + Low + " and " + High + "."
    if(myVal.length > 0) { 
	if (!isFloat(myVal) || (myVal < Low || myVal > High)) { 
            alert(themessage);
            setTimeout("focusElement('" + elem.form.name + "', '" + elem.name + "')", 0); }
      }
   }

//------------------------------------------------------------------------------------
function trim(s) {
  s = s.replace(/(^\s*)|(\s*$)/gi,"");
  s = s.replace(/[ ]{2,}/gi," ");
  s = s.replace(/\n /,"\n");
  return s;
}

//------------------------------------------------------------------------------------
function verifyAdd() {
  var theMessage = "Are you sure you want to add these members to the group?";
  if (confirm(theMessage)) { 
      document.formAddMembers.ASubmit.value = "ASubmit";
      document.formAddMembers.submit(); 
  } else { 
      document.formAddMembers.ASubmit.value = "";
      document.formAddMembers.addWhich.value = "";
      return false;
  }
}

//------------------------------------------------------------------------------------
function verifyDelete() {
  var theMessage = "Are you sure you want to delete these members from the group?";
  if (confirm(theMessage)) { 
      document.formDeleteMembers.DSubmit.value = "DSubmit";
      document.formDeleteMembers.submit(); 
  } else { 
      document.formDeleteMembers.DSubmit.value = "";
      document.formDeleteMembers.delWhich.value = "";
      return false;
  }
}

//------------------------------------------------------------------------------------
function checkEmail(myField) {
  var email = document.getElementById(myField);
  var reEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/;
  if (!reEmail.test(email.value)) {
      alert('Please provide a valid email address');
      email.focus;
      return false;
  }
}

//------------------------------------------------------------------------------------
function isPhoneNumber(myField) {
  // Check for correct phone number
  var phoneRe = /^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/;
  var p = document.getElementById(myField);
  var rePhoneNumber = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
  var intPhoneNumber = /^\+(?:[0-9] ?){6,14}[0-9]$/;
  if (!phoneRe.test(p.value)) {
      alert("Invalid Phone Number! \nU.S. numbers must be entered as: (555) 555-1234\nInternational numbers must have a leading '+' sign\nfollowed by groups of digits separated only\nby spaces.");
      p.focus;
      return false;
  }
}

//------------------------------------------------------------------------------------
function isURL(myField) { 
  var v = new RegExp(); 
  var url = document.getElementById(myField);
  v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$"); 
  if (!v.test(url.value)) { 
      alert("Please supply a valid URL."); 
      url.focus;
      return false; 
  } 
} 

//------------------------------------------------------------------------------------
  function verifyCompilation(myForm) {
    var themessage = "Please enter the following fields: ";
    // Put in the required fields like this:
    // if (document.form.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    if (document.formComp.Description.value=="") { themessage = themessage + " - Description";}
    if (themessage == "Please enter the following fields: ") {
        document.formComp.Action.value = 'Submit';
        submitForm(document.getElementById(myForm), "WriteCompilationRecord.php", "recordView");
    } else {
        return false;
    }
  }

//------------------------------------------------------------------------------------
  function verifyAnnotation(myForm) {
    var themessage = "Please enter the following fields: ";
    var badStop = "";
    // Put in the required fields like this:
    // if (document.formAnn.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    if (document.formAnn.AnnotationTypeId.value=="") { themessage = themessage + " - Annotation Type";}
    if (document.formAnn.Description.value=="") { themessage = themessage + " - Description";}
//    if (document.formAnn.KeyWords.value=="") { themessage = themessage + " - Keywords"; }
//    if (document.formAnn.SecondsOut.value <= document.formAnn.SecondsIn.value)  { badStop = "The stop marker is set to a time before the start marker--please reset it"; }
    if (themessage == "Please enter the following fields: " && badStop == "") {
        document.formAnn.Action.value = 'Submit'; 
        submitForm(document.getElementById(myForm), "WriteAnnotationRecord.php", "annotations");
        wavesurfer.clearRegions();
        fetchAnnotationMarkers(document.formAnn.TrackId.value);
    } else {
        if (badStop != "") { alert(badStop); } else { alert(themessage); }
        return false;
    }
  }


//------------------------------------------------------------------------------------
function verifyUser(myForm, myUserId, myDest) {
  var themessage = "Please enter the following fields: ";
  // Put in the required fields like this:
  // if (document.form.MyField.value=="") { themessage = themessage + " - MyField";}
  //alert if fields are empty and cancel form submit
  if (document.formReg.UserName.value=="") { themessage = themessage + " - Name";}
  if (document.formReg.UserAddress.value=="") { themessage = themessage + " - Address";}
  if (document.formReg.UserPhone.value=="") { themessage = themessage + " - Phone";}
  if (document.formReg.UserEmail.value=="") { themessage = themessage + " - E-mail";}

  if (myUserId == 0 || myUserId == null) {
      // Only check password fields if this is a new entry, where UserId is either 0 or null...
      if (document.formReg.UserPw.value=="") { themessage = themessage + " - Password";}
      if (document.formReg.UserPwReminder.value=="") { themessage = themessage + " - Password Reminder";}
  }

  if (themessage == "Please enter the following fields: ") {
      document.formReg.Action.value = 'Submit'; 
      if (myDest = 1) { 
          submitForm(document.getElementById(myForm), "WriteRegistration.php", "recordView");
      } else {
          submitForm(document.getElementById(myForm), "WriteUserRecord.php", "recordView");
      }
  } else {
      alert(themessage);
      return false;
  }
}

//------------------------------------------------------------------------------------
  function verifyPW(myForm, myDiv) {
    var f = myForm;
    var themessage = "";
    // Put in the required fields like this:
    // if (document.form.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    var t = f.elements[0].value;
    t = t.trim();
    if (t=="") { themessage = themessage + " + Password is missing.";}

    var t2 = f.elements[1].value;
    t2 = t2.trim();
    if (t2=="") { themessage = themessage + " + Retype Password is missing.";}
    if (t != t2) { themessage = themessage + " + Passwords do not match.";}

    var t3 = f.elements[2].value;
    t3 = t3.trim();
    if (t3=="") { themessage = themessage + " + Password Reminder is missing.";}

    if (themessage == "") {
        f.elements[0].value = t; 
        f.elements[1].value = t2; 
        f.elements[2].value = t3; 
        f.elements[3].value = 'Submit'; 
        submitForm(myForm, "ResetPassword.php", myDiv);
    } else {
        alert(themessage);
        return false;
    }
  }

//------------------------------------------------------------------------------------
  function showElements(oForm) {
    str = "Form Elements of form " + oForm.name + ": \n"
    for (i = 0; i < oForm.length; i++) {
         str += oForm.elements[i].name + "\n";
         alert(str);
    }
  }

//------------------------------------------------------------------------------------
  function verifyRecording(myForm) {
    var themessage = "The following fields are required: ";
    // Put in the required fields like this:
    // if (document.formRec.MyField.value=="") { themessage = themessage + " - MyField";}
    //alert if fields are empty and cancel form submit
    if (document.formRec.Title.value=="") { themessage = themessage + " - Title";}
    if (document.formRec.Subject.value=="") { themessage = themessage + " - Subject";}
    if (document.formRec.Creator.value=="") { themessage = themessage + " - Creator";}
    if (document.formRec.Source.value=="") { themessage = themessage + " - Source";}
    if (themessage == "The following fields are required: ") {
        document.formRec.action = "WriteRecordingMetadata.php"; 
        document.formRec.Action.value = "Submit"; 
        submitForm(myForm, "WriteRecordingMetadata.php", "recordView");
    } else {
        alert(themessage);
        return false;
    }
  }

//------------------------------------------------------------------------------------
  function listForms() {
    var formsCollection = document.getElementsByTagName("form");
    for(var i=0;i<formsCollection.length;i++)
       {
        alert(i + " -> " + formsCollection[i].name);
       }
  }