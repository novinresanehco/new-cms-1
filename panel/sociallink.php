<?php
define('head',true);
define('page','sociallink');
$pageTheme ='sociallink.htm';
$pagetitle = 'لینک های شبکه های اجتماعی';
define('tabs', true);
$tabs = array('درج لینک','مدیریت لینک ها');
include('header.php');
$htpl->showit();
$tpl->showit();
$ftpl->showit();