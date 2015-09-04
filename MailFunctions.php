<?php
  require_once("instanceSettings.php");
  require_once "Mail.php";

  //-----------------------------------------------------------
  // sendEMail ($to, $from, $subject, $message)
  //-----------------------------------------------------------
  function sendEMail($to, $from, $subject, $message) {
    // $host where XXXX is the name of the server where your account is located
    $host = "ssl://mail.olive.arvixe.com";
    $port = "465";
    // $username and $password are the uid & pwd of your account
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
    $mail = $smtp->send($to, $headers, $message);
    if (PEAR::isError($mail)) :
        echo("<p>" . $mail->getMessage() . "</p>");
    endif;
    return true;
  }
