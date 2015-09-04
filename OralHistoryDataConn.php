<?php
  // MySQL IP Connection
  $MySqlHostname = "localhost";
  $MySqlUsername = "OHPEditor";
  $MySqlPassword = "OHPEdit2014";
  $MySqlDatabase = "oralhistory";

  /* make connection to database...if no connection made, display error Message */
  $conn=mysqli_connect($MySqlHostname, $MySqlUsername, $MySqlPassword, $MySqlDatabase) OR DIE("Unable to connect to database");       

//-----------------------------------------
function clean($input) 
{ global $conn;
  //remove whitespace...
  $input = trim($input);
  //disable magic quotes...
  $input = get_magic_quotes_gpc() ? stripslashes($input) : $input;
  //prevent sql injection...
  $input = is_numeric($input) ? $input : mysqli_real_escape_string($conn, $input);
  //prevent xss...
  $input = htmlspecialchars($input);
  return $input;
}

//-----------------------------------------
function array_map_recursive($fn, $arr) {
    $rarr = array();
    foreach ($arr as $k => $v) {
        $rarr[$k] = is_array($v)
            ? array_map_recursive($fn, $v)
            : $fn($v); // or call_user_func($fn, $v)
    }
    return $rarr;
}

if (!empty($_GET)) { $_GET = array_map_recursive("clean", $_GET); }
if (!empty($_POST)) { $_POST = array_map_recursive("clean", $_POST); }

?>