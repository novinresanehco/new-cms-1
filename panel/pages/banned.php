<?php
define('samanehper','banned');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];
switch ($task){case "setting":
$host  		=  (!isset($_POST['host'])) ? ptintpm("allneed") : $_POST['host'];
$user  		=  (!isset($_POST['user'])) ? ptintpm("allneed") : $_POST['user'];
$pass 		=  (!isset($_POST['pass'])) ? ptintpm("allneed") : $_POST['pass'];
$mailppack	=  (!isset($_POST['mailppack'])) ? ptintpm("allneed") : $_POST['mailppack'];
$msppack	=  (!isset($_POST['msppack'])) ? ptintpm("allneed") : $_POST['msppack'];
$q = $d->uquery("nls",array(
'SmtpHost'=>$host,
'SmtpUser'=>$user,
'SmtpPassword'=>$pass,
'mailperpack'=>$mailppack,
'msperpack'=>$msppack,
));
ptintpm("setting_edited","success");
break;

case "new":
$ip   =  (empty($_POST['ip'])) ? ptintpm("allneed") : safe($_POST['ip'],1);
$pm   =  (empty($_POST['pm'])) ? ptintpm("allneed") : safe($_POST['pm'],1);
$num = $d->getrows("SELECT * FROM `bann` WHERE `ip`='$ip' LIMIT 1",true);
if($num == 0)
$d->iquery("bann",array("ip"=>$ip,"mess"=>$pm));
ptintpm("ok","success");
break;
case "delete":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `bann`  WHERE `id`='$id' LIMIT 1");
ptintpm("ok","success");
break;
default:
ptintpm();
}
?>