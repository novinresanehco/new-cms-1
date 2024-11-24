<?php
define("samanehper",'uc');
define("ajax_head",true);
define('theme','quickups.htm');
require("ajax_head.php");
$handle = opendir('../../'.updir);
    $bclist='';
    $rand = rand(100,2500);
    $select_theme='';
    while ($file = readdir($handle)) {    $ext= get_ext($file);
    if (( $file != '.') AND ($file != '..')  AND ($file != '') AND ($file != 'index.php')AND ($file != 'index.html')AND ($file != '.htaccess')  AND $ext!='php') {
    $bclist .= "(samaneh_files__)".$rand.$file;
    }
    }
    closedir($handle);
    $bclist = explode("(samaneh_files__)".$rand, $bclist);
    sort($bclist);
    for ($i=0; $i < sizeof($bclist); $i++) {
    if(!empty($bclist[$i])) {    $Size = @filesize ('../../'.updir."/".$bclist[$i]);
    $Size = (empty($Size)) ? 0 : round($Size/1024,2);
    $tpl -> block('listtr',  array(
    'name'    => $bclist[$i],
    'size'  => $Size,
    )
    );
    }
    }
	$tpl->assign('dir',$config['dir']);
$tpl->showit();
?>