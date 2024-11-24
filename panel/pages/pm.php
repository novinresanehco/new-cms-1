<?php
define("samanehper",'inbox');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout();
}
$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];
switch ($task){case "post_msg":
$reciver =  (empty($_POST['reciver'])) ? ptintpm("allneed") : $_POST['reciver'];
$title 	 =  (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
$text	 =  (empty($_POST['text'])) ? ptintpm("allneed") : $_POST['text'];
if($reciver =='[samaneh::all]'){$qu =  $d->Query("SELECT `u_id` FROM `member`");
	while($data = $d->fetch($qu)){	$q = $d->iquery("msg",array(
	'title'		=>$title,
	'text'		=>$text,
	'send_id'	=>$info['u_id'],
	're_id'		=>$data['u_id'],
	));
	}
}else{
$rid     = $user->GetId($reciver);
$rid 	 = ($rid == '0') ? ptintpm("usernotexit") : $rid ;
$q = $d->iquery("msg",array(
'title'=>$title,
'text'=>$text,
'send_id'=>$info['u_id'],
're_id'	=>$rid,
));
}
ptintpm("pm_sended","success");
break;

case "delete_pm":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `msg`  WHERE `pm_id`='$id' AND `re_id`='{$info['u_id']}' LIMIT 1");
ptintpm("pm_deleted","success");
break;
case "delete_pmo":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `msg`  WHERE `pm_id`='$id' AND `send_id`='{$info['u_id']}' LIMIT 1");
ptintpm("pm_deleted","success");
break;
case "read_pm":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$folder = (@$_POST['folder'] == 'inbox')  ? 'inbox' : 'outbox';
$hide =  ($folder == 'inbox') ? 'ajax_tabs_1_content_1' : 'ajax_tabs_2_content_2';
$qu = $d->Query("SELECT * FROM `msg`  WHERE `pm_id`='$id' AND `re_id`='{$info['u_id']}' LIMIT 1");
$data = $d->fetch($qu);
$html = new html();
global $lang;
$lang = $lang['msg'];
$sender_info = ($folder == 'outbox') ? $info : $user->info(false,$data['send_id']);
$info = ($folder == 'inbox') ? $info : $user->info(false,$data['re_id']);
$msg  = $lang['title'].$lang['sp'].$data['title'].$lang['nl'];
$msg .= $lang['sender'].$lang['sp'].$sender_info['showname'].$lang['sp2'].$lang['realname'].$lang['sp2'].$sender_info['name'].$lang['sp2'].$lang['username'].$lang['sp2'].$sender_info['user'].$lang['nl'];
$msg .= $lang['reciver'].$lang['sp'].$info['showname'].$lang['sp2'].$lang['realname'].$lang['sp2'].$info['name'].$lang['sp2'].$lang['username'].$lang['sp2'].$info['user'].$lang['nl'];
$msg .= $lang['text'].$lang['sp'].$data['text'].$lang['nl'];
$msg .= $html->href($lang['hide'],'#',$lang['hide'],'','hideid("'.$hide.'")');
$html->msg($msg,'info');
$html->printout();
break;
default:
ptintpm();
}
?>