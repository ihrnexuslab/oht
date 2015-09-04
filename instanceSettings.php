<?php

  // These key values are for the Recapcha function.
  $privatekey = "6Lch18YSAAAAAA3ulHPEqdpdBhjSiqSqo501EWjJ";
  $publickey = "6Lch18YSAAAAAO2Qkr_2GAugpYY0fscJEHrKbMQl";

  // The PublishByDefault variable is used to determine whether new recordings have
  // the "publish" flag in the Recordings table set to 1 (true) or 0 (false).
  $PublishByDefault = 1;

  // The AdminsCanDelete variable determines whether a regular Administrator can
  // delete entries in the database.  The default value is 0, because it's pretty
  // dangerous to let any admin delete records, since they don't usually grasp the
  // cascading nature of a relational database.
  $AdminsCanDelete = 0;

  // The PrefixCode is used to create different instances of the Oral History Project
  // using the same MySQL database, and keep them separate for different web instances.
  // This value is stored in the Compilations, User, and Recordings tables, so that the 
  // database can be sorted by web instance and queries can be restricted to web instance.
  $DefaultPrefixCode = "OHP";

  // If $RestrictQueriesToThisInstance is set to 1, keyword searches will only look for
  // results in this instance of the web application (using the value of $DefaultPrefixCode.
  // If it is set to 0 the search will find results across all the web instances in the database.
  // However, if the user is logged on as the super admin (where UserId = 1) then this will have
  // no effect (the super admin always sees everything from everywhere).
  $RestrictQueriesToThisInstance = 0;

  // If $UseCompilationsUsers is set to 1, the "My Compilations" search routine from the
  // user menu will pass through the Compilations2User table to pick up the compilations
  // that the user is a member of, and it will return the user's permissions for that
  // compilation.  The user's permissions get passed to the compilationPermissions Javascript
  // object, so they can be used to determine what further options the user has for the 
  // compilation and its recordings.
  $UseCompilationsUsers = 0;

  // This is the path to audio files that will be stored on the server for this instance.
  $filePath = "../OHP/tracks/";

  // The $DefaultCompilationId value should be set to 0 if there will be multiple
  // compilations allowed in a given web page instance.  If the value is > 0, then
  // when recordings are added to the MySQL database they are associated with this
  // value. It should be the CompilationId value of the correct compilation record
  // in the MySQL database. Values > 0 also determine whether buttons and links to
  // compilations related functions are exposed in the application. If they are not
  // exposed, in places where the normal web application has a button or link to list
  // compilations, the web app will have a button or link to show the recordings for
  // the $DefaultCompilationId value instead.
  $DefaultCompilationId = 0;

  // The $ShowRecordId flag is used to display the RecordingId number in the record
  // if the flag is set to 1.
  $ShowRecordId = 1;

  // Mail settings

  $Moderator = "Mark.Tebeau@asu.edu";

  // $from is your Arvixe account
  $from = "admin <admin@oralhistorytools.org>";
  // $host where XXXX is the name of the server where your account is located
  $host = "ssl://mail.olive.arvixe.com";
  $port = "465";
  // $username and $password are the uid & pwd of your account
  $username = "admin@oralhistorytools.org";
  $password = "OhP2015";
  $emailSub = "the Oral History Toolkit";
  $emailFrom = "-The Oral History Toolkit Team";

  // $SendEmailonNewRecording controls whether to send the mioderator an email when a user adds a new recording.
  $SendEmailOnNewRecording == 1;

  // $SendAdminEmailOnRegistration sends a e-mail message to the Moderator when a new user registers.
  $SendAdminEmailOnRegistration == 1;

  // This is the default length, in seconds, of transcription blocks.
  $DefaultTranscriptionBlockLength = 5;

  // This sets the minimum word length to create keyword searches from descriptions
  $MinSearchWordLength = 5;

  // This flag determines whether the public can register (1) or only Admins can register (0) new users
  $AllowPublicRegistration = 1;

  // These are the default settings for the flags in the User record.
  // The user flags get set to these values when a new user record is added.
  $NewUserAnnotatesAll = 0;
  $NewUserAnnotatesOwn = 1;
  $NewUserAdds = 1;
  $NewUserModifies = 1;
  $NewUserUploads = 1;
  $NewUserDownloads = 0;
  $NewUserIsAdmin = 0;

  // These are the default settings for the flags in the Compilation2User record.
  // The user flags get set to these values when a new compilation2user record is added.
  $CompUserIsAdmin = 1;
  $CompUserCanAnnotate = 1;
  $CompUserCanAdd = 1;
  $CompUserCanModify = 1;
  $CompUserCanUpload = 1;
  $CompUserCanDownload = 0;
?>