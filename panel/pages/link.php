<?php
define('samanehper','link');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];
switch ($task){case "add_link":
$url   =  (empty($_POST['url'])) ? ptintpm("allneed") : $_POST['url'];
$title =  (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
$desc =  (empty($_POST['desc'])) ? ptintpm("allneed") : $_POST['desc'];

$q = $d->iquery("link",array(
'title'=>$title,
'url'=>$url,
'des'=>$desc,
));
ptintpm("link_created","success");
break;

case "edit_link":
$edit_title   =  (empty($_POST['edit_title'])) ? ptintpm("allneed") : $_POST['edit_title'];
$edit_url =  (empty($_POST['edit_url'])) ? ptintpm("allneed") : $_POST['edit_url'];
$edit_desc =  (empty($_POST['edit_desc'])) ? ptintpm("allneed") : $_POST['edit_desc'];
$editing_id =  (!is_numeric(@$_POST['editing_id'])) ? ptintpm("allneed") : $_POST['editing_id'];
$d->uquery("link",array("title"=>$edit_title,"url"=>$edit_url,"des"=>$edit_desc),"id=".$editing_id);
ptintpm("cat_edited","success");
break;
case "delete_link":
$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `link`  WHERE `id`='$id' LIMIT 1");
ptintpm("link_deleted","success");
break;
default:
ptintpm();
}
?>