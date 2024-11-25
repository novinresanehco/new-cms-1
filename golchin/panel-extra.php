<?php
define('head',true);
define('extra','block');
define('page','extra');
$pageTheme ='extra.htm';
$pagetitle = 'صفحات اضافي';
define('tabs', true);
$tabs = array('درج صفحه','مديريت صفحه ها', 'ویرایش صفحه');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();