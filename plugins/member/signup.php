<?php
if(!defined('plugins-inc') OR !is_array(@$data))
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
if(isset($login))
if($login)
{
HEADER('LOCATION: index.php');
die('samaneh.com');
}
$itpl = new samaneh();
$itpl-> load('plugins/member/reg.html');
include('lang.fa.php');
if($config['new_member'] == '2')
{
$show_posts = false;
$itpl-> assign(array('Error'=>1,'ErrorMsg'=>$lang['disabled']));
}
else
{
	$required = array(
		'user'		=>$lang_signup['username'],
		'pass'		=>$lang_signup['password'],
		're_pass'	=>$lang_signup['re_pass'],
		'email'		=>$lang_signup['email'],
		'samaneh'	=>$lang_signup['scode'],
		'name'		=>$lang_signup['name'],
		);
	$optional = array('show','yahoo','gmail','tell','text_about');
	$show_posts = false;
	if(isset($_POST['user']))
	$_POST['user'] = ($_POST['user'] == 'guest') ? '' : $_POST['user'];
	if(isset($_GET['task']))
	{
		if(($_GET['task'] == 'dopost'))
		{
			$error = array();
			$itpl -> assign('signup_Send',1);
				foreach($required as $key=>$value)
					if(empty($_POST[$key]))
						$error[] = str_replace('%name%',$value,$lang_signup['required']);
				foreach($optional as $key=>$value)
					if(!isset($_POST[$key]))
						$_POST[$key] = '';
			
			if(count($error) == 0)
			{
				if( $_SESSION['CMS_secimg'] !== $_POST['samaneh'])
				$error[] = $lang_signup['wrongseccode'];
				else
				$_SESSION['CMS_secimg'] = md5(rand(1000,100000));
				if( !ctype_alnum( $_POST['user'] ) )
				$error[] = $lang_signup['userg'];
				elseif(strlen($_POST['user']) < $config['min_user_length'])
				$error[] = str_replace(array('%name%','%least%'),array($lang_signup['username'],$config['min_user_length']),$lang_signup['short']);
				elseif($d->getrows("SELECT `u_id` FROM `member` WHERE `user`='$_POST[user]' LIMIT 1",true) > 0)
				$error[] = $lang_signup['taken'];
				if($_POST['pass'] !== $_POST['re_pass'])
				$error[] = $lang_signup['mpass'];
				elseif(strlen($_POST['pass']) < $config['min_pass_length'])
				$error[] = str_replace(array('%name%','%least%'),array($lang_signup['password'],$config['min_pass_length']),$lang_signup['short']);
				$_POST['pass'] = md5(sha1($_POST['pass']));
				if(!email($_POST['email']))
				$error[] =$lang_signup['wmail'];
			}
			if(count($error) != 0)
			{
				$itpl-> assign('Error',1);
				$itpl-> assign('signup_form',1);
				$feilds = $d->Query( "SELECT * FROM `fields` ORDER BY `orderid`" );
				while($data = $d->fetch( $feilds ))
				{
					$itpl->block( 'regfields', array(
					'name'	=>	$data['name'],
					'title'	=>	$data['title'],
					'value'	=>	!empty( $_POST[$data['name']] ) ? $_POST[$data['name']] : '',
					) );
				}
				foreach($optional as $key)
					if(isset($_POST[$key]))
						$itpl-> assign('reg_'.$key,@$_POST[$key]);
				foreach($required as $key=>$value)
					if(isset($_POST[$key]))
						$itpl-> assign('reg_'.$key,@$_POST[$key]);
				$msg = ''; 
				foreach($error as $err)
				$msg .= $err.$lang_signup['seprator'];
				$itpl-> assign('ErrorMsg',$msg);
			}
			else
			{
				$insert_data = array(
				'prv'		=>	'',
				'name'		=>	$_POST['name'],
				'user'		=>	$_POST['user'],
				'pass'		=>	$_POST['pass'],
				'date'		=>	time(),
				'ip'		=>	getRealIpAddr(),
				'email'		=>	$_POST['email'],
				'yid'		=>	$_POST['yahoo'],
				'gid'		=>	$_POST['gmail'],
				'tell'		=>	$_POST['tell'],
				'about'		=>	$_POST['text_about'],
				'showname'	=>	$_POST['show'],
				'color'		=>	'#000000',
				'stat'		=>	'1',
				'avatar'	=>	'',
				);
				$feilds = $d->Query( "SELECT * FROM `fields` ORDER BY `orderid`" );
				while($data = $d->fetch( $feilds ))
				{
					$insert_data[$data['name']] = !empty( $_POST[$data['name']] ) ? $_POST[$data['name']] : '';
				}
				$d->iquery( "member", $insert_data );
				$itpl-> assign(array('Succeed'=>1,'Msg'=>$lang_signup['registred']));
			}
		}
		
	}
	else
	{
		$itpl-> assign('signup_form',1);
		$feilds = $d->Query( "SELECT * FROM `fields` ORDER BY `orderid`" );
		while($data = $d->fetch( $feilds ))
		{
			$itpl->block( 'regfields', array(
			'name'	=>	$data['name'],
			'title'	=>	$data['title'],
			'value'	=>	'',
			) );
		}
		foreach($optional as $key)
			$itpl-> assign('reg_'.$key,'');
		foreach($required as $key=>$value)
			$itpl-> assign('reg_'.$key,'');
	}
}
$itpl-> assign('register_rulles', nl2br($config['register_rulles']));
$tpl -> block('mp',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?plugins=member',
			'title' 	=> $lang_signup['signup'],
			'body'  	=> $itpl->dontshowit(),
			)
			);

?>