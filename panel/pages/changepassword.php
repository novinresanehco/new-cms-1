<?php
define('samanehper','changepassword');
define("ajax_head",true);
require("ajax_head.php");
function printpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg(isset($lang[$msg]) ? $lang[$msg] : $msg,$type);
$html->printout(true);
}
$pass   =  (empty($_POST['pass'])) 		? printpm("allneed") : $_POST['pass'];
$repass =  (empty($_POST['repass'])) 	? printpm("allneed") : $_POST['repass'];
$old 	=  (empty($_POST['old'])) 		? printpm("allneed") : $_POST['old'];
if($pass != $repass)
{
	printpm("passesdosentmatch");
	exit;
}
//check if old pass in corrent!
if(isset($info['u_id']) && is_numeric($info['u_id']))
{
	$pass = $d->Query("SELECT `pass` FROM `member` WHERE `u_id`='$info[u_id]' LIMIT 1");
	if($d->GetRows($pass) !== 1) printpm("error");
	$pass = $d->fetch($pass);
	if($pass['pass'] !== md5(sha1($old)))
	{
		printpm("wrongoldpass");
	}
	else
	{
		//check for min pass length
		if(isset($config['min_pass_length']) && is_numeric($config['min_pass_length']))
		{
			if(strlen($repass) < $config['min_pass_length'])
			{
				$min = str_replace('%min',$config['min_pass_length'],$lang['minpasslength']);
				printpm($min);
			}
			else
			{
				$repass = md5(sha1($repass));
				$d->Query("UPDATE `member` SET `pass`='$repass' WHERE `u_id`='$info[u_id]' LIMIT 1");
				printpm("ok","success");
			}
		}
	}
}
else
{
	printpm("error");
}
printpm("error");
