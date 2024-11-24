<?php
define('head',true);
define('page','flashnews');
$pageTheme ='flashnews.htm';
define('tabs', true);
$tabs = array('درج خبر', 'مديريت خبرها');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();