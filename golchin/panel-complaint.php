<?php
define('head',true);
define('page','complaint');
$pageTheme ='complaint.htm';
$pagetitle = 'لینک ها';
define('tabs', true);
$tabs = array('ثبت شکایت');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();