<?php
define('head',true);
define('page','uc');
$pageTheme ='upload.htm';
$pagetitle = 'مرکز آپلود';
define('tabs', true);
$tabs = array('تنظيمات','آپلود فايل','ليست فايل ها');
include('header.php');
$html = new html();
$html->ts('one',$config['random_name'],2);
$tpl->assign(array(
'formats'	=>	$config['allow_types'],
'one'		=>	$config['max_file_size'],
'total'		=>	$config['max_combined_size'],
'nums'		=>	$config['file_files'],
'dir'		=>	$config['dir'],
));
for($i=0;$i<$config['file_files'];$i++)
$tpl->block('numfiles',array());
$htpl->showit();
$tpl->showit();
$ftpl->showit();