<?php
define('samanehper','backup');
define("ajax_head",true);
define('theme','quickback.htm');
require("ajax_head.php");
    $handle = opendir('../backup');
    $bclist='';
    $select_theme='';
    while ($file = readdir($handle)) {
    if (( $file != '.') AND ($file != '..')  AND ($file != '') AND ($file != 'index.php')AND ($file != 'index.html')AND ($file != '.htaccess') ) {
    $nfile = str_replace('samanehBackup_','',$file);
    if($nfile == $file)
    continue;
    $file = $nfile;
    $nfile = str_replace('.php','',$file);
    if($nfile == $file)
    continue;
    $file = $nfile;
    $bclist .= "$file ";
    }
    }
    closedir($handle);
    $bclist = explode(" ", $bclist);
    sort($bclist);
    for ($i=0; $i < sizeof($bclist); $i++) {
    if(!empty($bclist[$i])) {
    $tpl -> block('listtr',  array(
    'fname'    => 'samanehBackup_'.$bclist[$i].'.php',
    'name'  => mytime('l j F Y | h:i:s A',$bclist[$i],$config['dzone']),
    )
    );
    }

    }
$tpl->showit();
?>