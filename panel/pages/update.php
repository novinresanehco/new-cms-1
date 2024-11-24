<?php
define('samanehper','update');
define("ajax_head",true);
require("ajax_head.php");
define('crrentver','3.0.0.0');
@$file = file_get_contents("http://sv.samaneh.ir/update.php?current=".crrentver);
if(!$file)
{$html->msg($lang['serverfailed']);
$html->printout();
}
echo $file;

?>