<?php
/////////////////////////////////////////////////////////////////////
// module name:     Autolinker
// module Version:  1.1.0
// Designer Name:   Sheikh Ali Shahi (BestTools.ir)
// Website:         www.besttools.ir
// Blog:            www.blog.besttools.ir
// Contact:         http://www.besttools.ir/contact/?t=autolinker1
// Designer SMS:    +98 935 935 9833
// License:         GPL/GNU (Free)
// Support:         http://rashcms.com/forum/forum-14.html
/////////////////////////////////////////////////////////////////////
define("rashcmsper",'autolinker');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];
switch ($task){

case "save":
$url       =(empty($_POST['url'])) ? ptintpm("allneed") : $_POST['url'];
$title 	   =(empty($_POST['title']))? ptintpm("allneed") : $_POST['title'];
$form      =(!is_numeric(@$_POST['sform'])) ? ptintpm() : $_POST['sform'];
$showl     =(!is_numeric(@$_POST['list'])) ? ptintpm() : $_POST['list'];
$time2     =(!is_numeric(@$_POST['time2'])) ? ptintpm() : $_POST['time2'];
$time3     =(!is_numeric(@$_POST['time3']) or @$_POST['time3'] <= @$_POST['time2'] ) ? ptintpm() : $_POST['time3'];
$timban    =(!is_numeric(@$_POST['timeban'])) ? ptintpm() : $_POST['timeban'];
$q = $d->uquery("autolinkerset",array(
'title'=>$title,
'url'=>$url,
'list'=>$showl,
'time2'=>$time2,
'time3'=>$time3,
'fstats'=>$form,
'bantime'=>$timban,
));
ptintpm("setting_edited","success");
break;
default:
ptintpm();
}

?>