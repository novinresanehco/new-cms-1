<?php
define('samanehper','upload');
define("ajax_head",true);
include('ajax_head.php');
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout();
}
$formats= (!empty($_POST['formats'])) ? $_POST['formats'] : ptintpm('allneed') ;
$one 	= (is_numeric(@$_POST['one'])) ? $_POST['one'] : ptintpm('allneed') ;
$total 	= (is_numeric(@$_POST['total'])) ? $_POST['total'] : ptintpm('allneed') ;
$random = (is_numeric(@$_POST['random'])) ? $_POST['random'] : ptintpm('allneed') ;
$nums 	= (is_numeric(@$_POST['nums'])) ? $_POST['nums'] : ptintpm('allneed') ;
$nums 	= ($nums > 10) ? 10 : $nums ;
$nums 	= ($nums < 1 ) ? 1  : $nums ;

$data = array(
'allow_types'=>$formats,
'max_file_size'=>$one,
'max_combined_size'=>$total,
'random_name'=>$random,
'file_files'=>$nums,
);
foreach($data as $key => $value)
{
	saveconfig($key, $value);
}
ptintpm('setting_edited','success');
?>