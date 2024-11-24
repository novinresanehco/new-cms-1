<?php
define('head',true);
define('page','changepassword');
$pageTheme ='changepassword.htm';
$pagetitle = 'تغییر رمز مدیریت';
define('tabs', true);
$tabs = array('تغییر رمز مدیریت');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();