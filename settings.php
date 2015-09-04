<?php
  // Full path to put into links.
  // This should be the home folder on the server where the app will be deployed.
  $pos = strrpos($_SERVER['PHP_SELF'], "/");
  if ($pos === false) : // note: three equal signs
      // not found...
      $fullPath = "http://" . $_SERVER['SERVER_NAME'];
  else :
      $fullPath = "http://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'],0,$pos);
  endif;

  // This is the web page title that appears in the header and title blocks of the
  // web page instance.
  $PageTitle = "The ASU Social Scribe Project Homepage";

  // $myHome is the variable that contains the default links in the linkBar <div>
  // It also gets loaded into the JavaScript variable, myHome, for use in the login and logout functions.
  $myHome = " <a class=headerlinks href='#' onClick='clearAll();'>$PageTitle</a>";

?>
<script type="text/javascript">
  // Save PHP $myHome as JavaScript myHome.
  // It will be used when the user logs in or out.
  myHome = "<?php echo $myHome; ?>";
</script>
<?php
require_once("instanceSettings.php");
?>