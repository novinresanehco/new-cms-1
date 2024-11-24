<?php
define("samanehper",'upload');
@set_time_limit(0);
error_reporting(7);
define("ajax_head",true);
include('ajax_head.php');
$html = new html();
function ptintpm($msg = 'waccess',$type = 'error'){$msg = ($msg == 'waccess') ? $lang['waccess'] : $msg;
global $lang,$html;
$html->msg($msg,$type);
$html->printout(true);
}
$task = (@$_POST['task'] !='delete') ? printpm() : 'delete';
$name = (empty($_POST['name'])) ? printpm() : safeurl($_POST['name'],false);
$site = $config['site'];
$dir  = updir.'/';
$folder="../../".$dir;
$allow_types = explode(',',$config['allow_types']);
$ext_count = count($allow_types);
$i=0;
$types='';
$lang = $lang['upload'];
foreach($allow_types AS $extension) {
	If($i <= $ext_count-2) {
		$types .="*.".$extension.", ";
	} Else {
		$types .="*.".$extension;
	}
	$i++;
}
unset($i,$ext_count);
$error="";
		$ext= get_ext($name);
			If(!in_array($ext, $allow_types)) {				ptintpm($lang['wrongext'].$types);			}
unlink($folder.$name);
ptintpm($lang['deleted'],'success');
?>
