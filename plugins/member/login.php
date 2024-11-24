<?php
if(!defined('plugins-inc') OR !is_array(@$data))
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
include('lang.fa.php');
$itpl = new samaneh();
$itpl-> load('plugins/member/login.html');
$show_posts = false;
if(isset($_POST['submit'])){
if(empty($_POST['user']) || empty($_POST['pass']))
$error = $lang['allneed'];
elseif(!$user->checkimg(@$_POST['samaneh'],$config['tries']))
$error = $lang['wrongseccode'];
elseif($user->login($_POST['user'],$_POST['pass'])){
if(!empty($_SERVER['HTTP_REFERER']))
$add = @parse_url($_SERVER['HTTP_REFERER']);
$add = $add['host'];
$add2 = @parse_url($config['site']);
$add2 = $add2['host'];
if($add == $add2)
HEADER("LOCATION: ".$_SERVER['HTTP_REFERER']);
else
HEADER("LOCATION: index.php?plugins=member&method=index");
die();
}else{
$_SESSION['tries'] = intval(@$_SESSION['tries'])+1;
$error = $lang['wup'];
}
}
else
{
	if($login)
	HEADER("LOCATION: index.php?plugins=member&method=profile");
	$itpl->assign('login_form',1);
	
}
if(!empty($error))
$itpl->assign(array('Error'=>1,'ErrorMsg'=>$error,'login_form'=>1));

$itpl->assign('rand',rand(1,5000000));
if(@$_SESSION['tries'] >= $config['tries'])
$itpl->assign('LoginSec',1);
$tpl -> block('mp',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?plugins=member',
			'title' 	=> $lang_signup['login'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
?>