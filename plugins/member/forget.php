<?php
if(!defined('plugins-inc') OR !is_array(@$data))
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
include('lang.fa.php');
$itpl = new samaneh();
$itpl-> load('plugins/member/forget.html');
$show_posts = false;
if(isset($_GET['submit']))
	{
	$error = array();
	$required = array(
	'username'	=> $lang_signup['username'],
	'email'		=> $lang_signup['email'],
	'samaneh'	=> $lang_signup['scode'],
	);
	foreach($required as $key=>$value)
		if(empty($_POST[$key]))
			$error[] = str_replace('%name%',$value,$lang_signup['required']);
	if(count($error) == '0')
		{
			if(!email($_POST['email']))
				$error[] = $lang_signup['wmail'];
			if($user->GetId($_POST['username']) <= 0)
				$error[] = $lang_signup['wuser'];
			if( $_SESSION['CMS_secimg'] !== $_POST['samaneh'])
				$error[] = $lang_signup['wrongseccode'];
		}
	if(count($error) == '0')
		{
		$m = $d->getrowvalue("email","SELECT `email` FROM `member` WHERE `user`='$_POST[username]' LIMIT 1",true);
		if($m != $_POST['email'])
			$error[] = $lang_signup['wmail'];
		}
	if(count($error) == '0')
		{
		$min_pass_length = ($config['min_pass_length']<5) ? 5 : $config['min_pass_length'];
		$new_pass = GEN($min_pass_length);
		$body = str_replace(array('%password%','%user%'),array($new_pass,$_POST['username']),$lang_signup['pass_recovery']);
		$new_pass = md5(sha1($new_pass));
		send_mail($m,$config['email'],$body,$lang_signup['forget']);
		$d->Query("UPDATE `member` SET `pass`='$new_pass' WHERE `user`='$_POST[username]' LIMIT 1");
		$itpl-> assign(array('Succeed'=>1,'Msg'=>$lang_signup['pass_resetted']));
		}
	else
		{
		$msg = '';
		foreach($error as $err)
		$msg .= $err.$lang_signup['seprator'];
		$itpl-> assign(array('Error'=>1,'ErrorMsg'=>$msg,'Form'=>1));
		}
	}
	else
	$itpl-> assign('Form',1);
$tpl -> block('mp',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?plugins=member',
			'title' 	=> $lang_signup['forget'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
?>