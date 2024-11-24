<?php
set_time_limit(0);
define("samanehper",'newsletter');
define("ajax_head",true);
require("ajax_head.php");
function printpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? printpm() : $_POST['task'];
switch ($task){case "setting":
$host  		=  (!isset($_POST['host'])) ? printpm("allneed") : $_POST['host'];
$user  		=  (!isset($_POST['user'])) ? printpm("allneed") : $_POST['user'];
$pass 		=  (!isset($_POST['pass'])) ? printpm("allneed") : $_POST['pass'];
$mailppack	=  (!isset($_POST['mailppack'])) ? printpm("allneed") : $_POST['mailppack'];
$msppack	=  (!isset($_POST['msppack'])) ? printpm("allneed") : $_POST['msppack'];
$q = $d->uquery("nls",array(
'SmtpHost'=>$host,
'SmtpUser'=>$user,
'SmtpPassword'=>$pass,
'mailperpack'=>$mailppack,
'msperpack'=>$msppack,
));
printpm("setting_edited","success");
break;

case "new":
$mails   =  (empty($_POST['mails'])) ? printpm("allneed") : $_POST['mails'];
$mails = explode(',',$mails);
foreach($mails as $mail){
if(!empty($mail))
if(email($mail))
$d->iquery("nl",array("mail"=>$mail));
}
printpm("ok","success");
break;
case "send":
$title   =  (empty($_POST['title'])) ? printpm("allneed") : $_POST['title'];
$text    =  (empty($_POST['mail_text'])) ? printpm("allneed") : $_POST['mail_text'];
$q = $d->Query("SELECT `mail` FROM `nl`");
$nls = $d->Query("SELECT * FROM nls");
$nls = $d->fetch($nls);
$us = false;
    if(!empty($nls['SmtpHost']) && !empty($nls['SmtpUser']) && !empty($nls['SmtpPassword']))
	$us = true;
	$c = 0;
while($data = $d->fetch($q))
{
if(!$us)
send_mail($data['mail'],$config['email'],$text,$title);
else
{
	if($c < $nls['mailperpack'] OR (!empty($nls['mailperpack']) AND !empty($nls['msperpack'])))
	{
	send_mail($data['mail'],$config['email'],$text,$title);
	$c++;
	}
	else
	{
	delay($nls['msperpack']);
	$c = 0;
	}
}
}
printpm("ok","success");
break;
case "delete":
$id =  (!is_numeric(@$_POST['id'])) ? printpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `nl` WHERE `id`='$id' LIMIT 1");
printpm("ok","success");
break;
default:
printpm();
}
?>