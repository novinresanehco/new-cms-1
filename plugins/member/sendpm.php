<?php
if(!defined('plugins-inc') OR !is_array(@$data))
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new samaneh();
$itpl-> load('plugins/member/sendpm.html');
$itpl->assign('theme_url',core_theme_url);
include('lang.fa.php');
$show_posts = false;
if(!$login)
$itpl->assign(array('Error'=>1,'Msg'=>$lang['limited_area']));
else{
	$itpl->assign('User',1);
	$_GET['task']	= empty($_GET['task']) ? 'none' : $_GET['task'];
	switch($_GET['task'])
	{
	case 'send':
	$required = array(
	'reciver'	=> $lang_signup['reciver'],
	'title'		=> $lang_signup['title'],
	'text'		=> $lang_signup['text'],
	);
	$error = array();
	foreach($required as $key=>$value)
		if(empty($_POST[$key]))
			$error[] = str_replace('%name%',$value,$lang_signup['required']);
	if($_POST['reciver'] == $user->info['user'])
	$error[] = $lang_signup['same_re_send'];
	if(count($error) == 0)
		{
		$q = $d->Query("SELECT `u_id` FROM `member` WHERE `user`='$_POST[reciver]' LIMIT 1");
		if($d->getrows($q) != '1')
		$error[] = $lang_signup['wrong_reciver'];
		}
	if(count($error) == 0)
		{
		$q = $d->fetch($q);
		$d->iquery("msg",array(
		'send_id'	=> $user->info['u_id'],
		're_id'		=> $q['u_id'],
		'text'		=> $_POST['text'],
		'title'		=> $_POST['title'],
		));
		$itpl->assign(array('Succeed'=>1,'Msg'=>$lang_signup['pm_sent']));
		}
		else
		{
		$itpl-> assign('Form',1);
		$itpl-> assign('Error',1);
		foreach($required as $key=>$value)
			$itpl-> assign($key,@$_POST[$key]);
		$msg = ''; 
		foreach($error as $err)
		$msg .= $err.$lang_signup['seprator'];
		$itpl-> assign('Msg',$msg);
		}
	
	break;
	case 'none':
	$required = array(
	'reciver','title','text');
	$itpl->assign('Form',1);
	foreach($required as $key)
			$itpl-> assign($key,'');
	break;
	default:
	$itpl->assign(array('Error'=>1,'Msg'=>$lang['404']));
	}
}
$tpl -> block('mp',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?plugins=member',
			'title' 	=> $lang_signup['sendpm'],
			'body'  	=> $itpl->dontshowit(),
			)
			);

?>