<?php
define('samanehper','sociallink');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];
switch ($task){case "add_sociallink":
$url   =  (empty($_POST['url'])) ? ptintpm("allneed") : $_POST['url'];
$title =  (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
$icon =  (empty($_POST['icon'])) ? ptintpm("allneed") : $_POST['icon'];

$q = $d->iquery("sociallink",array(
'title'=>$title,
'url'=>$url,
'icon'=>$icon,
));
ptintpm("ok","success");
break;

case "edit_sociallink":
$edit_title   =  (empty($_POST['edit_title'])) ? ptintpm("allneed") : $_POST['edit_title'];
$edit_url =  (empty($_POST['edit_url'])) ? ptintpm("allneed") : $_POST['edit_url'];
$edit_icon =  (empty($_POST['edit_icon'])) ? ptintpm("allneed") : $_POST['edit_icon'];
$editing_id =  (!is_numeric(@$_POST['editing_id'])) ? ptintpm("allneed") : $_POST['editing_id'];
$d->uquery("sociallink",array("title"=>$edit_title,"url"=>$edit_url,"icon"=>$edit_icon),"id=".$editing_id);
ptintpm("ok","success");
break;
case "delete_sociallink":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `sociallink`  WHERE `id`='$id' LIMIT 1");
ptintpm("ok","success");
break;
default:
ptintpm();
}