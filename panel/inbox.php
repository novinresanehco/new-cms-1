<?php
define('head',true);
define('page','inbox');
$pageTheme ='inbox.htm';
$pagetitle = 'پیغام خصوصی';
define('tabs', true);
$tabs = array('ارسال پيغام خصوصي','صندوق ورودي','صندوق خروجي');
include('header.php');
$page = (is_numeric(@$_GET['page'])) ? $_GET['page'] : '0';
$task = '';
if(isset($_GET['task']))
$task = $_GET['task'];
if($task == 'inbox'){$tpl->assign('ipage',$page);
$tpl->assign('opage',0);
$page = ($page != '0') ? $tpl->block('inbox',array()) : '';
}elseif($task == 'outbox'){
$tpl->assign('opage',$page);
$tpl->assign('ipage',0);
$page = ($page != '0') ? $tpl->block('outbox',array()) : '';
}else{$tpl->assign('opage',0);
$tpl->assign('ipage',0);
}
$htpl->showit();
$tpl->showit();
$ftpl->showit();