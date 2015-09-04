<?
    function GetRandomKey($myTable, $myKeyField) 
    {   global $conn;
        $testNum = 1;
        While ($testNum >= 1) :
	      $myRand = mt_rand(5000000,2000000000);
	      $strSel = "SELECT COUNT(*) FROM " . $myTable . " WHERE " . $myKeyField . " = " . $myRand;
	      $test1 = mysqli_query($conn, $strSel);
	      $test2 = mysqli_fetch_row($test1);
	      $testNum = $test2[0];
        endwhile;
        $b = mysqli_free_result($test1);
        return ($myRand); 
    } // end function

//----------------------------------
    function TheNextKeyValue($myKey, $myTable) 
    {   global $conn;
	$query = "SELECT " . $myKey . " FROM " . $myTable . " WHERE " . $myKey . " <= 9999999999 ORDER BY " . $myKey . " DESC"; 
	$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row = mysqli_fetch_row($result);
	$retval = $row[0] + 1;
        $b = mysqli_free_result($result);
	return $retval;
    } // end function

/*-------------------------------------------------------------------------------------------------------
  function TheNextPhotoName($myKey, $myLastPic, $myTable, $myKeyFld) 

  This routine is a generic way of incrementing picture file names
  based on the accession number of the object being photographed.  

  Fields are as follows:

  $myKey	: The Accession Number of the object being photographed, e.g. '1950.001.00001'
  $myLastPic	: The name of the field in the item table that indicates the last picture letter.
  		  Pictures are appended with a, b, c, etc. and the routine below increments this
		  letter in the parent table when it assignes a letter for the new picture.
  $myTable	: The name of the item's parent table, e.g. 'ethnology', 'archaeology' etc.
  $myKeyFld	: The name of the key field in the parent table.

  Example	: $picname = TheNextPhotoName ("1950.001.00001", "LastPic", "ethnology", "AccessionNo")
--------------------------------------------------------------------------------------------------------*/

    function TheNextPhotoName($myKey, $myLastPic, $myTable, $myKeyFld) 
    {   global $conn;
	$query = "SELECT $myLastPic FROM $myTable WHERE $myKeyFld = '$myKey'";
	$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row = mysqli_fetch_row($result);
	$myLetter = $row[0];
	if($myLetter=="") :
	   $myLetter = "a";
	else:
	   $myLetter = chr(ord($myLetter)+1);
	   if (ord($myLetter) > 122) { $myLetter = "A"; }
 	endif;
        $query="UPDATE $myTable SET $myLastPic = '$myLetter' WHERE $myKeyFld = '$myKey'";
	$result = mysqli_query($conn,$query);
	$retval = $myLetter . ".jpg";
        $b = mysqli_free_result($result);
	return $retval;
    } // end function

//--------------------------------------
   function IncrementAccessionNumber($myId)
   {   global $conn;
       $Qla = "SELECT AccNum,LastAccessionNo FROM accessions WHERE AccId = '$myId'";
       $resultQla = mysqli_query($conn, $Qla);
       $numberQla = mysqli_Numrows($resultQla);
       $AccNum=mysqli_result($resultQla,0,"AccNum");
       $NextAcc=mysqli_result($resultQla,0,"LastAccessionNo");
       if ($NextAcc == 0) : 	// There are no current Accession Items...
           $NextAcc = 1;
       else : 			// Increment the LastAccessionNo number...
           $NextAcc++;
       endif;

       // Update the LastAccessionNo field in the accessions table...
       $Qla = "UPDATE accessions SET LastAccessionNo='$NextAcc' WHERE AccId = '$myId'";
       $resultQla = mysqli_query($conn, $Qla);

       $ThisAcc = "00000" . $NextAcc;
       $ThisAcc = substr($ThisAcc,-1,5);
       $NewAccessionNo = $AccNum . "." . $ThisAcc;
       $b = mysqli_free_result($resultQla);

       return $NewAccessionNo;
    } // end function
?>