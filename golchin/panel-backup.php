<?php
define('head',true);
define('page','backup');
$pageTheme ='backup.htm';
$pagetitle = 'پشتيبان گيري';
define('tabs', true);
$tabs = array('ايجاد پشتيبان','مديريت پشتيبان ها');
include('header.php');
$html = new html();
$htpl->showit();
$tpl->showit();
$ftpl->showit();