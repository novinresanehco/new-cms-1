<?php
define('samanehper','block');
define("ajax_head",true);
require("ajax_head.php");
function printpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
$task = (!isset($_POST['task'])) ? printpm() : $_POST['task'];
switch ($task){case "add_block":
$position = array(1,2,3,4,5);
$show 	  = array(1,2,3);
$position   =  (!in_array(@$_POST['position'],$position)) ? printpm() : $_POST['position'];
$show =  (!in_array(@$_POST['show'],$show)) ? printpm() : $_POST['show'];
$title =  (empty($_POST['title'])) ? printpm("allneed") : $_POST['title'];
$plugins =  (empty($_POST['plugins'])) ? printpm("allneed") : $_POST['plugins'];
if($plugins != 'none')
if($d->getrows("SELECT `id` FROM `plugins` WHERE `name`='$plugins'",true) <= 0)
printpm();
$text =  (empty($_POST['text'])) ? printpm("allneed") : $_POST['text'];
$order = $d->getmax('order','block');
$order++;
$q = $d->iquery("block",array(
'name'=>$title,
'pos'=>$position,
'users'=>$show,
'plugins'=>$plugins,
'text'=>mysql_real_escape_string($text),
'order'=>$order,
));
printpm("block_created","success");
break;

case "edit_block":
$position = array(1,2,3,4,5);
$show 	  = array(1,2,3);
$position   =  (!in_array(@$_POST['position'],$position)) ? printpm() : $_POST['position'];
$show =  (!in_array(@$_POST['show'],$show)) ? printpm() : $_POST['show'];
$title =  (empty($_POST['title'])) ? printpm("allneed") : $_POST['title'];
$plugins =  (empty($_POST['plugins'])) ? printpm("allneed") : $_POST['plugins'];
if($plugins != 'none')
if($d->getrows("SELECT `id` FROM `plugins` WHERE `name`='$plugins'",true) <= 0)
printpm();
$text =  (empty($_POST['text'])) ? printpm("allneed") : $_POST['text'];
$id =  (!is_numeric(@$_POST['id'])) ? printpm() : $_POST['id'];
$q = $d->uquery("block",array(
'name'=>$title,
'pos'=>$position,
'users'=>$show,
'plugins'=>$plugins,
'text'=>mysql_real_escape_string($text),
),"id=".$id." LIMIT 1 ");
printpm("block_edited","success");
break;
case "delete_block":
$id =  (!is_numeric(@$_POST['id'])) ? printpm() : $_POST['id'];
$qu = $d->Query("DELETE FROM `block`  WHERE `id`='$id' LIMIT 1");
printpm("block_deleted","success");
break;
default:
printpm();
}
?>