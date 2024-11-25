<?php
define('samanehper','member');
define("ajax_head",true);
require("ajax_head.php");
function printpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? printpm() : $_POST['task'];
switch ($task){case "post_setting":

$active_reg 	=  (@$_POST['active_reg'] == 1) ? 1 : 2;
$user_list 		= @$_POST['user_list'];
$user_list 		=  (is_numeric($user_list) && $user_list<4 && $user_list>=1) ? $user_list : printpm();
$min_user_len 	= @$_POST['min_user_len'];
$min_user_len 	=  (is_numeric($min_user_len) && $min_user_len<9 && $min_user_len>3) ? $min_user_len : printpm();
$min_pass_len 	= @$_POST['min_pass_len'];
$min_pass_len 	=  (is_numeric($min_pass_len) && $min_pass_len<10 && $min_pass_len>4) ? $min_pass_len : printpm();
$allow_send_pm 	=  (@$_POST['allow_send_pm'] == 1) ? 1 : 2;
$allow_send_post=  (@$_POST['allow_send_post'] == 1) ? 1 : 2;
$register_rulles = isset($_POST['register_rulles']) ? $_POST['register_rulles']: '';
$data = array(
'new_member'=>$active_reg,
'user_list'=>$user_list,
'min_user_length'=>$min_user_len,
'register_rulles'=>$register_rulles,
'min_pass_length'=>$min_pass_len,
'send_pm'=>$allow_send_pm,
'send_post'=>$allow_send_post,
);
foreach($data as $name => $value)
saveconfig($name, $value);
printpm("setting_edited","success");
break;
case "edit_user":
$username  	= (!empty($_POST['username'])) ? safe($_POST['username'],1) : printpm('fillnd');
$email  	= (!empty($_POST['email'])) ? safe($_POST['email'],1) : printpm('fillnd');
$name  		= (!empty($_POST['name'])) ? safe($_POST['name'],1) : printpm('fillnd');
$about  	= (isset($_POST['about'])) ? safe($_POST['about'],1) : printpm();
$showname  	= (!empty($_POST['showname'])) ? safe($_POST['showname'],1) : printpm('fillnd');
$yid  		= (isset($_POST['yid'])) ? safe($_POST['yid'],1) : printpm();
$gid  		= (isset($_POST['gid'])) ? safe($_POST['gid'],1) : printpm();
$tell  		= (isset($_POST['tell'])) ? safe($_POST['tell'],1) : printpm();
$avatar  	= (isset($_POST['avatar'])) ? safe($_POST['avatar'],1) : printpm();
$id  		= (is_numeric(@$_POST['id'])) ? $_POST['id'] : printpm();
$stat  		=  (is_numeric(@$_POST['stat']) && (@$_POST['stat']== 1 || @$_POST['stat']== 2)) ? $_POST['stat'] : printpm();
$q = $d->uquery("member",array(
'user'=>$username,
'name'=>$name,
'email'=>$email,
'about'=>$about,
'showname'=>$showname,
'yid'=>$yid,
'gid'=>$gid,
'tell'=>$tell,
'stat'=>$stat,
'avatar'=>$avatar,
),"u_id=".$id." LIMIT 1 ");
printpm("user_edited","success");
break;
case "delete_user":
$id =  (!is_numeric(@$_POST['id'])) ? printpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `member`  WHERE `u_id`='$id' LIMIT 1");
printpm("page_deleted","success");
break;
case "update":
$id  = (is_numeric(@$_POST['usrid'])) ? $_POST['usrid'] : printpm();
$ac  = (is_numeric(@$_POST['accsess_admin_acc'])) ? $_POST['accsess_admin_acc'] : printpm();
$ac	 = ($ac == 0 || $ac ==1) ? $ac : printpm();
//$q = $d->Query("SELECT * FROM `menus` WHERE `type`='1'");
$q = $d->Query("SHOW COLUMNS FROM `permissions`");

	if(!$d->getrows($q))
	printout("error");
	if($d->getrows($d->Query("SELECT `u_id` FROM `permissions`  WHERE `u_id` ='$id' LIMIT 1")) <= 0)
		{
		while($da = $d->fetch($q))
			$arr[$da['Field']] = '0';
		$arr['u_id'] = $id;
		$d->iquery("permissions",$arr);
		unset($arr);unset($q);
		$q = $d->Query("SHOW COLUMNS FROM `permissions`");
		}
	if($ac == 1)
	{
	$data = $d->fetch($q);
		do{
    	$name = $data['Field'];
		if(isset($_POST[$name]))
		if($_POST[$name] == 1 || $_POST[$name] == 0)
		$d->Query("UPDATE `permissions` SET `$name`='$_POST[$name]' WHERE `u_id` ='$id' LIMIT 1");
		}while($data = $d->fetch($q));
	$d->Query("UPDATE `permissions` SET `access_admin_area`='1' WHERE `u_id`='$id' LIMIT 1");
	}
	else
	while($data = $d->fetch($q)){
	$name = $data['Field'];
	if($name == 'usrid' || $name == 'u_id')
		continue;
	
		if($name == 'newpost' || $name == 'comment'){
		
			if($name == 'newpost')
			$_POST[$name] = $_POST['sendpost'];
			if($name == 'comment')
			$_POST[$name] = $_POST['sendcomment'];
		}
		else
		$_POST[$name] = 0;
		$d->Query("UPDATE `permissions` SET `$name`=$_POST[$name] WHERE `u_id` ='$id' LIMIT 1");
	$d->Query("UPDATE `permissions` SET `access_admin_area`='0' WHERE `u_id`='$id' LIMIT 1");
	}
printpm("user_edited","success");
break;
default:
printpm();
}
?>