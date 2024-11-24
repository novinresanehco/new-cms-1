<?php
define('head',true);
define('page','banned');
$pageTheme ='banned.htm';
$pagetitle = 'لیست سیاه';
define('tabs', true);
$tabs = array('درج آي پي','لیست سیاه');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();