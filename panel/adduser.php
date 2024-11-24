<?php
define('head',true);
define('page','member');
$pageTheme ='adduser.htm';
$pagetitle = 'افزودن مدیر';
define('tabs', true);
$tabs = array('افزودن مدیر');
include('header.php');
$html = new html();
$htpl->showit();
$tpl->showit();
$ftpl->showit();
