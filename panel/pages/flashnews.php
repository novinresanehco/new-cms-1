<?php
define('rashcmsper','flashnews');
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
case "add_news":
$title =  (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
$link =  (empty($_POST['link'])) ? ptintpm("allneed") : $_POST['link'];
$q = $d->iquery("news",array(
'title'=>$title,
'link'=>$link,
));
ptintpm("ok","success");
break;

case "edit_news":
$edit_title   =  (empty($_POST['edit_title'])) ? ptintpm("allneed") : $_POST['edit_title'];
$edit_link   =  (empty($_POST['edit_link'])) ? ptintpm("allneed") : $_POST['edit_link'];
$editing_id =  (!is_numeric(@$_POST['editing_id'])) ? ptintpm("allneed") : $_POST['editing_id'];

$d->uquery("news",array("title"=>$edit_title,"link"=>$edit_link),"id=".$editing_id);
ptintpm("news_edited","success");
break;
case "delete_news":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `news`  WHERE `id`='$id' LIMIT 1");
ptintpm("ok","success");
break;
default:
ptintpm();
}