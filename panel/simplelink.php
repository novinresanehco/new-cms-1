<?php
define('head',true);
define('page','simplelink');
$pageTheme ='link.htm';
$pagetitle = 'لینک ها';
define('tabs', true);
$tabs = array('درج لینک','مدیریت لینک ها');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();