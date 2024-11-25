<?php
define('head',true);
define('page','managepostfields');
$pageTheme ='managepostfields.htm';
define('tabs', true);
$pagetitle = 'مدیریت فیلدهای مطلب';
$tabs = array('درج فیلد جدید', 'لیست فیلدها');
include('header.php');
if(is_numeric(@$_GET['moveup']))
{
	$tpl->block('move',array());
	$qu = $d->Query("SELECT `orderid`,`id` FROM `postfields` WHERE `orderid`<'{$_GET['moveup']}'  ORDER BY `orderid` DESC LIMIT 1");
	$qu = $d->fetch($qu);
	$lowerid = $qu['id'];
	$qu = $qu['orderid'];
	if(!empty($qu))
	{
		$d->Query("UPDATE `postfields` SET `orderid`='$qu' WHERE `orderid`='{$_GET['moveup']}'  LIMIT 1");
		$d->Query("UPDATE `postfields` SET `orderid`='{$_GET['moveup']}' WHERE `id`='$lowerid' LIMIT 1");
	}
}
elseif(is_numeric(@$_GET['movedown']))
{
	$tpl->block('move',array());
	$qu = $d->Query("SELECT `orderid`,`id` FROM `postfields` WHERE `orderid`>'{$_GET['movedown']}' ORDER BY `orderid` ASC LIMIT 1");
	$qu = $d->fetch($qu);
	$uperid = $qu['id'];
	$qu = $qu['orderid'];
	if(!empty($qu))
	{
		$d->Query("UPDATE `postfields` SET `orderid`='$qu' WHERE `orderid`='{$_GET['movedown']}' LIMIT 1");
		$d->Query("UPDATE `postfields` SET `orderid`='{$_GET['movedown']}' WHERE `id`='$uperid' LIMIT 1");
	}
}
$htpl->showit();
$tpl->showit();
$ftpl->showit();