<?php
require_once "Mail.php";
// $from is your Arvixe account
$from = "Oral History Tools Admin <admin@oralhistorytools.org>";
// $to is the receivers email
$to = "shsavage@asu.edu";
$subject = "Test email...";

// $tok is my prebuilt message body (Ex $tok = "First Name: .$fName."\nMiddle Initial: ".$mName;
$tok = "It works!";

$host = "ssl://mail.olive.arvixe.com";
$port = "465";
// $username and $password are the uid & pwd of your email account
$username = "admin@oralhistorytools.org";
$password = "OhP2015";

$headers = array ('From' => $from,
'To' => $to,
'Subject' => $subject);
$smtp = Mail::factory('smtp',
array ('host' => $host,
'port' => $port,
'auth' => true,
'username' => $username,
'password' => $password));

$mail = $smtp->send($to, $headers, $tok);

if (PEAR::isError($mail)) :
    echo("<p>" . $mail->getMessage() . "</p>");
else :
echo("<p>Message successfully sent!</p>");
endif;
?>