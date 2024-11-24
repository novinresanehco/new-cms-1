<?php
if(!defined('plugins-inc') OR !is_array(@$data))
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new samaneh();
$itpl-> load('plugins/member/profile.html');
include('lang.fa.php');
$show_posts = false;
$itpl->assign('theme_url',core_theme_url);
$publicprofile = false;

	if( (!isset( $_GET['user'] ) OR !ctype_alnum( $_GET['user'] )) && !$login )
	{
		$itpl->assign(array('Error'=>1,'Msg'=> $lang['limited_area']));
	}
	else if((isset( $_GET['user'] ) && ctype_alnum( $_GET['user'] )) )
	{
		$publicprofile = true;
		
		$user = $d->Query("SELECT * FROM `member` WHERE `user`='$_GET[user]' LIMIT 1");
		if( $d->getRows( $user ) !== 1)
		{
			$itpl->assign(array('Error'=>1,'Msg'=> $lang['404']));
		}
		else
		{
			$itpl = new samaneh();
			$itpl-> load('plugins/member/publicprofile.html');
			$primary_data = $data = $d->fetch( $user );
			
			$fields = $d->Query("SELECT * FROM `fields` ORDER BY `orderid`");
			while($idata = $d->fetch( $fields ) )
			{
				unset( $primary_data[$idata['name']] );
				if( !empty( $data[$idata['name']] ) )
				{
					$itpl-> block( 'fields', array( 
					'name'		=>		$idata['name'],
					'title'		=>		$idata['title'],
					'value'		=>		$data[$idata['name']],
					) );
				}
				
			}
			if( !empty( $data['avatar'] ) )
			{
				$itpl-> assign(array(
				'avatar'	=>		1,
				'avatarurl'	=>		$config['site'] . 'files/avatars/' . $info['avatar'],
				));
			}
			$itpl->assign( $primary_data );
		}
	}
if(!$login && !$publicprofile)
{
	$itpl->assign(array('Error'=>1,'Msg'=> $lang['limited_area']));
}
else if($login)
{
	$itpl->assign('User',1);
	$_GET['task'] = empty($_GET['task']) ? 'none' : $_GET['task'];
	switch($_GET['task'])
	{
	case 'edit':
	$itpl->assign(array(
	'Edit'					=>1,
	'pro_name'				=>$info['name'],
	'pro_username'			=>$info['user'],
	'pro_email'				=>$info['email'],
	'pro_yid'				=>$info['yid'],
	'pro_gid'				=>$info['gid'],
	'pro_avatar'			=>$info['avatar'],
	'pro_about'				=>$info['about'],
	'pro_showname'			=>$info['showname'],
	'pro_tell'				=>$info['tell'],
	'signup_date'			=>mytime($config['dtype'],$info['date'],$config['dzone']),
	));
	break;
	case 'doedit':
	$mp = array();
	$required = array(
//	'user'		=>$lang_signup['username'],
	'email'		=>$lang_signup['email'],
	'name'		=>$lang_signup['name'],
	);
	$error = array();
	$optional = array('showname','yid','gid','tell','about');
	foreach($required as $key=>$value)
			if(empty($_POST[$key]))
				$error[] = str_replace('%name%',$value,$lang_signup['required']);
			else
				$mp[$key] = $_POST[$key];
		
		foreach($optional as $key)
			if(!isset($_POST[$key]))
				$_POST[$key] = '';
			else
			$mp[$key] = $_POST[$key];
		if(!empty($_POST['pro_pass']))
			if(!empty($_POST['pro_new_pass']))
				{
				$u_id = $user->info['u_id'];
				$p = $d->getrowvalue("pass","SELECT `pass` FROM `member` WHERE `u_id`='$u_id' LIMIT 1",true);
				if($p == md5(sha1($_POST['pro_pass'])))
					{
						if(strlen($_POST['pro_new_pass']) < $config['min_pass_length'])
							$error[] = str_replace(array('%name%','%least%'),array($lang_signup['password'],$config['min_pass_length']),$lang_signup['short']);
						else
						$mp['pass'] = md5(sha1($_POST['pro_new_pass']));
					}
					else
					$error[] = $lang_signup['wp'];
				}
		if(isset( $_FILES['avatar'] ) && !empty( $_FILES["avatar"]["name"] ) )
		{
			@$extension = end(explode(".", $_FILES["avatar"]["name"]));
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			if ((($_FILES["avatar"]["type"] == "image/gif")
				|| ($_FILES["avatar"]["type"] == "image/jpeg")
				|| ($_FILES["avatar"]["type"] == "image/jpg")
				|| ($_FILES["avatar"]["type"] == "image/pjpeg")
				|| ($_FILES["avatar"]["type"] == "image/x-png")
				|| ($_FILES["avatar"]["type"] == "image/png"))
				//&& ($_FILES["avatar"]["size"] < 20000)
				&& in_array($extension, $allowedExts))
			{
				if ($_FILES["avatar"]["error"] > 0)
				{
					$error[] = $lang['error'];
				}
				else
				{
					$name = md5( sha1( $user->info['u_id'] ) ).'_' . time() . '_' . rand(1000,2000). '.' . $extension;
					$mp['avatar'] = $name;
					move_uploaded_file($_FILES["avatar"]["tmp_name"],"files/avatars/" . $name);
				}
			}
			else
			{
				$error[] = $lang_signup['avatarext'];
			}
			
		}		
		if(count($error) != 0)
		{
			$msg = ''; 
			foreach($error as $err)
			$msg .= $err.$lang_signup['seprator'];
			$itpl->assign(array('Error'=>1,'Msg'=>$msg));
		}
		else
		{
			$u_id = $user->info['u_id'];
			$d->uQuery("member",$mp,"`u_id`='$u_id' LIMIT 1");
			$itpl->assign(array('Succeed'=>1,'Msg'=>$lang['ok']));
		}
	break;
	case 'none':
	$itpl->assign(array(
	'core'				=>1,
	'name'				=>$info['name'],
	'username'			=>$info['user'],
	'email'				=>$info['email'],
	'signup_date'		=>mytime($config['dtype'],$info['date'],$config['dzone']),
	));
	if( !empty( $info['avatar'] ) )
			{
				$itpl-> assign(array(
				'avatar'	=>		1,
				'avatarurl'	=>		$config['site'] . 'files/avatars/' . $info['avatar'],
				));
			}
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
			'title' 	=> $lang_signup['profile'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
?>