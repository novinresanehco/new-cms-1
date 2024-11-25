<?php
define('head',true);
define('page','permission');
$pageTheme ='permission.htm';

$pagetitle = 'اختيارات';
define('tabs', true);
$tabs = array('ليست كاربران','ویرایش اختیارات');
include('header.php');
$html = new html();
$page = (is_numeric(@$_GET['page'])) ? $_GET['page'] : '0';$tpl->assign('page',$page);
$page = ($page != '0') ? $tpl->block('page',array()) : '';
$lc = array();
$q = $d->Query("SELECT * FROM `menus` WHERE `type`='1'");
while($c = $d->fetch($q))
$lc[$c['name']] = array('id'=>$c['id'],'name'=>$c['name']);
$q = $d->Query("SELECT * FROM `menus` WHERE `type`='1'");
$q = $d->Query("SHOW COLUMNS FROM `permissions`");
$menu = '';
$pconj = "'";
while($c = $d->fetch($q)){
if(!empty($c['Field']))
	{
	$conj = "','";
	
	if(isset($lc[$c['Field']]))
	$menu .= $pconj.$lc[$c['Field']]['name'].$conj;
	else
	$menu .= $pconj.$c['Field'].$conj;
	$pconj = '';
	$tpl->block("samaneh_per",array("id"=>$data['id'],"id2"=>$data['id'].$conj));
	}
}
$tpl->assign('samaneh_elements',substr($menu,0,-2));
$htpl->showit();
$tpl->showit();
$ftpl->showit();