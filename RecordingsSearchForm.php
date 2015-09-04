<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("settings.php");
  $o  = "        <!--// Keyword Search Form...-->\n";
  $o .= "        <form id='RKWSearch' name='RKWSearch'>\n";
  $o .= "          <table style='width:100%; font-size: 8.5pt' align='center' border = 2 rules=groups>\n";
  $o .= "            <thead>\n";
  $o .= "              <tr>\n";
  $o .= "                <th class='tblname'>Keyword Search:</td>\n";
  $o .= "              </tr>\n";
  $o .= "            </thead>\n";
  $o .= "            <tbody>\n";
  $o .= "              <tr>\n";
  $o .= "                <td>\n";
  $o .= "                  <input type='text' name='kw' id='kword' style='width: 100%;'>\n";
  $o .= "                </td>\n";
  $o .= "              </tr>\n";
  $o .= "              <tr>\n";
  $o .= "                <td>Check the resources you want to include.<br>\n";
  $o .= "                  Recordings: <input type='checkbox' checked='checked' id='Rec' value='0' />&nbsp; Annotations: <input type='checkbox' checked='checked' id='Ann' value='0' />&nbsp; Transcripts: <input type='checkbox' checked='checked' id='Trn' value='0' /> \n";
  $o .= "                </td>\n";
  $o .= "              </tr>\n";
  $o .= "              <tr>\n";
  $o .= "                <td align='center'>\n";
  $o .= "                  <input type=button class=b1 id='Submit' value='Search' onClick='showRecordKeyword(document.getElementById(\"kword\").value)'>&nbsp; \n";
  $o .= "                  <input type=button class=b1 id='Reset' value='Reset' onclick='document.RKWSearch.reset()'>\n";
  $o .= "                </td>\n";
  $o .= "              </tr>\n";
  $o .= "            </tbody>\n";
  $o .= "          </table>\n";
  $o .= "        </form>\n";
  echo $o;
?>